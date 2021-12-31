#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import argparse
import logging
import requests
from dataclasses import dataclass, field
import os
import sys
import re
from uuid import uuid4

"""
  XG Proyect Automatic install script
  code by @duhow <duhowpi.net>
"""

class Step:
    success = False
    def __init__(self, base_url, **kwargs):
        self.base_url = base_url
        self.body = {}
        for k in kwargs.keys():
            self.__setattr__(k, kwargs[k])

    @property
    def url(self):
        prefix = ""
        if not self.base_url.endswith("install"):
            prefix = "/install"
        return f"{self.base_url}{prefix}/index.php?page=installation&mode={self.page}"

    def prepare(self):
        """ Run actions before running """
        if self.page:
            self.body["page"] = self.page
        pass

    def run(self):
        """ Main action """
        logging.debug(f"POST {self.url}")
        r = requests.post(self.url, data=self.body, timeout=10, allow_redirects=True)
        assert r.status_code == 200, f"HTTP code {r.status_code}"
        assert "alert" not in r.text
        assert "Warning" not in r.text
        return True

    def revert(self):
        """ Revert actions if something went wrong during run """
        pass

    def post(self):
        """ Run post-actions after running"""
        pass

    def execute(self):
        logging.info(f"Running {type(self).__name__}")
        logging.debug("Running prepare")
        self.prepare()
        try:
            logging.debug("Running main action")
            self.run()
            logging.debug("Running post")
            self.post()
            self.success = True
        except AssertionError as msg:
            logging.warning(msg)
            #logging.warning("Something went wrong! Running revert")
            self.revert()

class CheckInstallAvailable(Step):
    page = "overview"

    def run(self):
        r = requests.get(self.url)
        assert r.status_code == 200, f"HTTP code {r.status_code}"
        assert "provide write permission" not in r.text, "Cannot write config.php file. Update config folder permissions."
        assert "XG Proyect is already installed" not in r.text, "XG Proyect is already installed!"
        assert "alert" not in r.text
        return True

class SetupConnectionData(Step):
    page = "step1"

    def prepare(self):
        self.body = {
            "host": self.mysql.host,
            "user": self.mysql.user,
            "password": self.mysql.password,
            "db": self.mysql.database,
            "prefix": self.mysql.table_prefix
        }
        self.body["page"] = self.page

    def run(self):
        logging.debug(f"POST {self.url}")
        r = requests.post(self.url, data=self.body, timeout=10, allow_redirects=True)
        assert r.status_code == 200, f"HTTP code {r.status_code}"
        assert "Unable to connect to the database" not in r.text, f"Cannot connect to database {self.mysql}"
        assert "Error writing the config.php file" not in r.text, "Cannot write config.php file. Update config folder permissions."
        assert "alert" not in r.text
        assert "Warning" not in r.text
        return True

class SetupCheckConnection(Step):
    page = "step2"

class SetupConfigFile(Step):
    page = "step3"

class SetupDatabaseData(Step):
    page = "step4"

class SetupAdminUser(Step):
    page = "step5"

    def prepare(self):
        self.body = {
            "adm_user": self.credentials.user,
            "adm_pass": self.credentials.password,
            "adm_email": self.credentials.email
        }
        self.body["page"] = self.page

    def run(self):
        logging.debug(f"POST {self.url}")
        r = requests.post(self.url, data=self.body, timeout=10, allow_redirects=True)
        assert "Unable to connect to the database" not in r.text
        assert "Error writing the config.php file" not in r.text
        assert "Database query failed" not in r.text, re.sub(r'<.*?>', '', r.text).strip()
        assert "alert" not in r.text
        assert "Warning" not in r.text
        assert r.status_code == 200, f"HTTP code {r.status_code}"
        return True

class CheckWebsite(Step):
    page = "index"

    def run(self):
        r = requests.get(self.base_url, allow_redirects=True)
        assert r.status_code == 200, f"HTTP code {r.status_code}"
        return True

@dataclass
class MysqlConfig:
    host: str = "127.0.0.1:3306"
    user: str = "root"
    password: str = field(repr=False, default="")
    database: str = "xgp"
    table_prefix: str = field(repr=False, default="xgp_")

@dataclass
class XGProyectCredentials:
    url: str = "http://localhost"
    user: str = "admin"
    password: str = field(repr=False, default="")
    email: str = field(repr=False, default="admin@example.com")

def main(args):
    xgp_data = XGProyectCredentials(
        args.base_url,
        args.admin_user,
        args.admin_password,
        args.admin_email
    )

    # generate default password
    if xgp_data.password == "":
        logging.warning("Admin password is empty!")
        xgp_data.password = uuid4()
        logging.warning(f"Password set to {xgp_data.password}")

    mysql_data = MysqlConfig(
        args.mysql_host,
        args.mysql_user,
        args.mysql_password,
        args.mysql_database
    )

    # steps to perform install
    steps = [
        CheckInstallAvailable,
        SetupConnectionData,
        SetupCheckConnection,
        SetupConfigFile,
        SetupDatabaseData,
        SetupAdminUser,
        CheckWebsite
    ]

    # execute install
    for stepclass in steps:
        step = stepclass(
            xgp_data.url,
            mysql=mysql_data,
            credentials=xgp_data
        )
        step.execute()
        if not step.success:
            sys.exit(1)

    logging.info("Success! Ensure to delete INSTALL folder!")

def parser():
    parser = argparse.ArgumentParser(description='XG Proyect Installer')
    parser.add_argument(
        "base_url", nargs="?",
        default=os.getenv("XGPROYECT_URL", "http://localhost"), help="Website location"
    )
    parser.add_argument(
        "-H", "--mysql-host", default=os.getenv("MYSQL_HOST", "127.0.0.1:3306"), help="MySQL hostname and port"
    )
    parser.add_argument(
        "-u", "--mysql-user", default=os.getenv("MYSQL_USER", "root"), help="MySQL username"
    )
    parser.add_argument(
        "-P", "--mysql-password", default=os.getenv("MYSQL_PASSWORD", ""), help="MySQL password"
    )
    parser.add_argument(
        "-d", "--mysql-database", default=os.getenv("MYSQL_DATABASE", "xgp"), help="MySQL database"
    )
    parser.add_argument(
        "-xu", "--admin-user", default=os.getenv("XGPROYECT_USER", "admin"), help="XG Proyect username"
    )
    parser.add_argument(
        "-xe", "--admin-email", default=os.getenv("XGPROYECT_EMAIL", "admin@example.com"), help="XG Proyect email"
    )
    parser.add_argument(
        "-xp", "--admin-password", default=os.getenv("XGPROYECT_PASSWORD", ""), help="XG Proyect password"
    )
    parser.add_argument(
        "-v", "--debug", action="store_true", help="Show debug messages"
    )
    return parser

if __name__ == '__main__':
    args = parser().parse_args()

    loglevel = logging.DEBUG if args.debug else logging.INFO
    logging.basicConfig(format='%(levelname)s: %(message)s', level=loglevel)

    main(args)
