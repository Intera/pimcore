<?php
/**
 * Created by IntelliJ IDEA.
 * User: Michi
 * Date: 11.11.2010
 * Time: 10:35:07
 */


class TestSuite_Rest_ObjectTest extends PHPUnit_Framework_TestCase {

    /**
     * creates a class called "unittest" containing all Object_Class_Data Types currently available.
     * @return void
     */
    public function testClassCreate() {

        $conf = new Zend_Config_Xml(TESTS_PATH . "/resources/objects/class-import.xml");
        $importData = $conf->toArray();

        $layout = Object_Class_Service::generateLayoutTreeFromArray($importData["layoutDefinitions"]);

        $class = Object_Class::create();
        $class->setName("unittest");
        $class->setUserOwner(1);
        $class->save();

        $id = $class->getId();
        $this->assertTrue($id > 0);

        $class = Object_Class::getById($id);

        $class->setLayoutDefinitions($layout);

        $class->setUserModification(1);
        $class->setModificationDate(time());

        $class->save();

    }


}
