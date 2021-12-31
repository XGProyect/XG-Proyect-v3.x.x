#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import argparse
import logging
import requests
from dataclasses import dataclass, field
import os
import sys
from uuid import uuid4

class Step:
    success = False
    def __init__(self, base_url, **kwargs):
        self.base_url = base_url
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
        pass

    def run(self):
        """ Main action """
        logging.debug(f"GET {self.url}")
        r = requests.get(self.url, timeout=10, allow_redirects=True)
        assert r.status_code == 200
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
        assert r.status_code == 200
        assert "provide write permission" not in r.text, "Cannot write config.php file. Update config folder permissions."
        assert "alert" not in r.text
        return True

class SetupDatabaseConfig(Step):
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
        assert r.status_code == 200
        assert "Unable to connect to the database" not in r.text, "Cannot connect to database"
        assert "Error writing the config.php file" not in r.text, "Cannot write config.php file. Update config folder permissions."
        assert "alert" not in r.text
        assert "Warning" not in r.text
        return True

class SetupConfig(Step):
    page = "step2"

class SetupDatabase(Step):
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
        assert r.status_code == 200
        assert "Unable to connect to the database" not in r.text
        assert "Error writing the config.php file" not in r.text
        assert "alert" not in r.text
        assert "Warning" not in r.text
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

    if not xgp_data.password:
        logging.warning("Admin password is empty!")
        xgp_data.password = uuid4()
        logging.warning(f"Password set to {xgp_data.password}")

    mysql_data = MysqlConfig(
        args.mysql_host,
        args.mysql_user,
        args.mysql_password,
        args.mysql_database
    )

    steps = [
        CheckInstallAvailable,
        SetupDatabaseConfig,
        SetupConfig,
        SetupDatabase,
        SetupAdminUser
    ]

    for stepclass in steps:
        step = stepclass(
            xgp_data.url,
            mysql=mysql_data,
            credentials=xgp_data
        )
        step.execute()
        if not step.success:
            sys.exit(1)

def parser():
    parser = argparse.ArgumentParser(description='XG Proyect Installer')
    parser.add_argument(
        "base_url", nargs="?",
        default="http://localhost", help="Website location"
    )
    parser.add_argument(
        "-H", "--mysql-host", default="127.0.0.1:3306", help="MySQL hostname and port"
    )
    parser.add_argument(
        "-u", "--mysql-user", default="root", help="MySQL username"
    )
    parser.add_argument(
        "-P", "--mysql-password", default="", help="MySQL password"
    )
    parser.add_argument(
        "-d", "--mysql-database", default="xgp", help="MySQL database"
    )
    parser.add_argument(
        "-xu", "--admin-user", default="admin", help="XG Proyect username"
    )
    parser.add_argument(
        "-xe", "--admin-email", default="admin@example.com", help="XG Proyect email"
    )
    parser.add_argument(
        "-xp", "--admin-password", default="xgproyect", help="XG Proyect password"
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
