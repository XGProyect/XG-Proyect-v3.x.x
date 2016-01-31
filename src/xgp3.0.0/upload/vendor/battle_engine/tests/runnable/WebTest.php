<?php

/**
 *  OPBE
 *  Copyright (C) 2013  Jstar
 *
 * This file is part of OPBE.
 * 
 * OPBE is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OPBE is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OPBE.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OPBE
 * @author Jstar <frascafresca@gmail.com>
 * @copyright 2013 Jstar <frascafresca@gmail.com>
 * @license http://www.gnu.org/licenses/ GNU AGPLv3 License
 * @version beta(26-10-2013)
 * @link https://github.com/jstar88/opbe
 */
require (".." . DIRECTORY_SEPARATOR . "RunnableTest.php");

class WebTest extends RunnableTest
{
    public function getAttachers()
    {
        return $this->buildPlayerGroup($_POST["attacker_tech"], $_POST["attacker_fleet"]);
    }
    public function getDefenders()
    {
        return $this->buildPlayerGroup($_POST["defender_tech"], $_POST["defender_fleet"]);
    }

    private function buildPlayerGroup($tech, $fleets)
    {
        $playerObj = new Player(1);
        $playerObj->setName('bot');
        $playerObj->setTech($tech['weapons'], $tech['shields'], $tech['armour']);
        foreach ($fleets as $idFleet => $fleet)
        {
            $fleetObj = new Fleet($idFleet);
            foreach ($fleet as $id => $count)
            {
                $count = floor($count);
                $id = floor($id);
                if ($count > 0 && $id > 0)
                {
                    $fleetObj->addShipType($this->getShipType($id, $count));
                }
            }
            if (!$fleetObj->isEmpty())
            {
                $playerObj->addFleet($fleetObj);
            }
        }
        if ($playerObj->isEmpty())
        {
            die("<meta http-equiv=\"refresh\" content=2;\"WebTest.php\">There should be at least an attacker and defender");
        }
        $playerGroupObj = new PlayerGroup();
        $playerGroupObj->addPlayer($playerObj);
        return $playerGroupObj;
    }
}

if (isset($_GET['vars']))
{
    $selectedVar = $_GET['vars'];
}
elseif (isset($_POST['vars']))
{
    $selectedVar = $_POST['vars'];
}
else
{
    $selectedVar = 'XG';
}
WebTest::includeVars($selectedVar);
if ($selectedVar == 'XG'){
    LangManager::getInstance()->setImplementation(new XGLangImplementation());
}
else{
    LangManager::getInstance()->setImplementation(new MoonsLangImplementation());
}


if (isset($_GET['good']))
{
    session_start();
    if (!isset($_SESSION['vote']))
    {
        $_SESSION['vote'] = true;
        $count = file_get_contents('good.txt');
        $count++;
        file_put_contents('good.txt', $count);
    }
    session_write_close();
}
elseif (isset($_GET['bad']))
{
    session_start();
    if (!isset($_SESSION['vote']))
    {
        $_SESSION['vote'] = true;
        $count = file_get_contents('bad.txt');
        $count++;
        file_put_contents('bad.txt', $count);
    }
    session_write_close();
}
if ($_POST)
{
    if (isset($_POST['report']))
    {
        $path = 'errors' . DIRECTORY_SEPARATOR . 'reports';
        if (!file_exists($path))
        {
            mkdir($path, 0777, true);
        }
        require_once 'HTMLPurifier' . DIRECTORY_SEPARATOR . 'HTMLPurifier.auto.php';
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $clean_html = $purifier->purify($_POST['report']);
        $clean_html = 'comment = ' . strip_tags($_POST['comment']) . PHP_EOL . $clean_html;
        file_put_contents($path . DIRECTORY_SEPARATOR . date('d-m-y__H-i-s') . '.html', $clean_html);

        $extra = 'WebTest.php';
        echo 'This battle has been reported.';
        die(header("refresh: 2; url= $extra"));

    }
    session_start();
    if (!isset($_SESSION['time']))
    {
        $_SESSION['time'] = time();
    }
    else
    {
        if (time() - $_SESSION['time'] < 3)
        {
            die('Sorry,to prevent malicious usage you can only execute one simulation each 3 seconds');
        }
        $_SESSION['time'] = time();
    }
    session_write_close();
    $count = file_get_contents('count.txt');
    $count++;
    file_put_contents('count.txt', $count);
    //inject html code in the report
    ob_start();
    new WebTest($_POST['debug'] === 'debug');
    $wb = ob_get_clean();
    $dom = new DOMDocument();
    $dom->loadHTML($wb);

    //inject the form

    $submit = $dom->createElement('input');
    $submit->setAttribute('type', 'submit');
    $submit->setAttribute('value', 'Report to admin');

    $name = $dom->createElement('input');
    $name->setAttribute('type', 'hidden');
    $name->setAttribute('name', 'report');
    $name->setAttribute('value', $wb); //not really good for performace but ok :)

    $comment = $dom->createElement('input');
    $comment->setAttribute('type', 'text');
    $comment->setAttribute('name', 'comment');
    $comment->setAttribute('value', 'insert a comment here');
    $comment->setAttribute('size', '100');

    $fieldset = $dom->createElement('fieldset');
    $fieldset->appendChild($submit);
    $fieldset->appendChild($name);
    $fieldset->appendChild($comment);

    $form = $dom->createElement('form');
    $form->setAttribute('method', 'POST');
    $form->appendChild($fieldset);

    $body = $dom->getElementsByTagName("body")->item(0);
    $body->insertBefore($form, $body->firstChild);

    echo $dom->saveHTML();
}
else
{
    $bad = file_get_contents('bad.txt');
    $good = file_get_contents('good.txt');
    $count = floor(file_get_contents('count.txt'));
    $list = WebTest::getVarsList();
    $reslist = WebTest::$reslist;
    $combatCaps = WebTest::$CombatCaps;
    $pricelist = WebTest::$pricelist;
    require ('WebTestGui.html');

}

?>
