<?php
/**
 * Pimcore
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.pimcore.org/license
 *
 * @category   Pimcore
 * @package    Object_Class
 * @copyright  Copyright (c) 2009-2010 elements.at New Media Solutions GmbH (http://www.elements.at)
 * @license    http://www.pimcore.org/license     New BSD License
 */

class Object_Class_Data_Datetime extends Object_Class_Data
{

    /**
     * Static type of this element
     *
     * @var string
     */
    public $fieldtype = "datetime";

    /**
     * Type for the column to query
     *
     * @var string
     */
    public $queryColumnType = "bigint(20)";

    /**
     * Type for the column
     *
     * @var string
     */
    public $columnType = "bigint(20)";

    /**
     * Type for the generated phpdoc
     *
     * @var string
     */
    public $phpdocType = "Zend_Date";


    /**
     * @var int
     */
    public $defaultValue;


    /**
     * @var bool
     */
    public $useCurrentDate;


    /**
     * @see Object_Class_Data::getDataForResource
     * @param Zend_Date $data
     * @param null|Object_Abstract $object
     * @return integer
     */
    public function getDataForResource($data, $object = null)
    {
        if ($data instanceof Zend_Date) {
            return $data->getTimestamp();
        }
    }

    /**
     * @see Object_Class_Data::getDataFromResource
     * @param integer $data
     * @return Zend_Date
     */
    public function getDataFromResource($data)
    {
        if ($data) {
            return new Pimcore_Date($data);
        }
    }

    /**
     * @see Object_Class_Data::getDataForQueryResource
     * @param Zend_Date $data
     * @param null|Object_Abstract $object
     * @return integer
     */
    public function getDataForQueryResource($data, $object = null)
    {
        if ($data instanceof Zend_Date) {
            return $data->getTimestamp();
        }
    }

    /**
     * @see Object_Class_Data::getDataForEditmode
     * @param Zend_Date $data
     * @param null|Object_Abstract $object
     * @return string
     */
    public function getDataForEditmode($data, $object = null)
    {
        if ($data instanceof Zend_Date) {
            return $data->getTimestamp();
        }
    }

    /**
     * @see Object_Class_Data::getDataFromEditmode
     * @param integer $data
     * @param null|Object_Abstract $object
     * @return Zend_Date
     */
    public function getDataFromEditmode($data, $object = null)
    {
        if ($data) {
            return new Pimcore_Date($data / 1000);
        }
        return false;
    }

    public function getDataForGrid($data, $object = null)
    {
        if ($data instanceof Zend_Date) {
            return $data->getTimestamp();
        } else {
            return null;
        }
    }

    /**
     * @see Object_Class_Data::getVersionPreview
     * @param Zend_Date $data
     * @return string
     */
    public function getVersionPreview($data)
    {
        if ($data instanceof Zend_Date) {
            return $data->get(Zend_Date::DATE_FULL);
        }
    }


    /**
     * converts object data to a simple string value or CSV Export
     * @abstract
     * @param Object_Abstract $object
     * @return string
     */
    public function getForCsvExport($object)
    {
        $key = $this->getName();
        $getter = "get" . ucfirst($key);
        if ($object->$getter() instanceof Zend_Date) {
            return $object->$getter()->toString();
        } else return null;
    }

    /**
     * fills object field data values from CSV Import String
     * @abstract
     * @param string $importValue
     * @param Object_Abstract $abstract
     * @return Object_Class_Data
     */
    public function getFromCsvImport($importValue)
    {
        try {
            $value = new Pimcore_Date(strtotime($importValue));
            return $value;
        } catch (Exception $e) {
            return null;
        }
    }


    /**
     * converts data to be exposed via webservices
     * @param string $object
     * @return mixed
     */
    public function getForWebserviceExport($object)
    {
        $key = $this->getName();
        $getter = "get" . ucfirst($key);
        if ($object->$getter() instanceof Zend_Date) {
            return $object->$getter()->toString();
        } else return null;
    }

    /**
     * converts data to be imported via webservices
     * @param mixed $value
     * @return mixed
     */
    public function getFromWebserviceImport($value)
    {
        $timestamp = strtotime($value);
        if (empty($value)) {
            return null;
        } else if ($timestamp !== FALSE) {
            return new Pimcore_Date($timestamp);
        } else {
            throw new Exception("cannot get values from web service import - invalid data");
        }
    }

    /**
     * @return Pimcore_Date
     */
    public function getDefaultValue()
    {
        if ($this->defaultValue !== null) {
            return $this->defaultValue;
            //return new Pimcore_Date($this->defaultValue);
        } else return 0;
    }

    /**
     * @param mixed $defaultValue
     * @return void
     */
    public function setDefaultValue($defaultValue)
    {
        if (strlen(strval($defaultValue)) > 0) {
            if (is_numeric($defaultValue)) {
                $this->defaultValue = (int)$defaultValue;
            } else {
                $this->defaultValue = strtotime($defaultValue);
            }

        }
    }


/**
        * Creates getter code which is used for generation of php file for object classes using this data type
        * @param $class
        * @return string
        */
       public function getGetterCode($class)
       {
           $key = $this->getName();
           $code = "";

           $code .= '/**' . "\n";
           $code .= '* @return ' . $this->getPhpdocType() . "\n";
           $code .= '*/' . "\n";
           $code .= "public function get" . ucfirst($key) . " () {\n";

           // adds a hook preGetValue which can be defined in an extended class
           $code .= "\t" . '$preValue = $this->preGetValue("' . $key . '");' . " \n";
           $code .= "\t" . 'if($preValue !== null && !Pimcore::inAdmin()) { return $preValue;}' . "\n";

           if (method_exists($this, "preGetData")) {
               $code .= "\t" . '$data = $this->getClass()->getFieldDefinition("' . $key . '")->preGetData($this);' . "\n";
           } else {
               $code .= "\t" . '$data = $this->' . $key . ";\n";
           }

           // insert this line if inheritance from parent objects is allowed
           if ($class->getAllowInherit()) {
               $code .= "\t" . 'if(!$data && Object_Abstract::doGetInheritedValues()) { return $this->getValueFromParent("' . $key . '");}' . "\n";
           }

           if ($this->useCurrentDate) {
               $code .= "\t" . 'if(!$data) { ' . "\n";
               $code .= "\t\t" . '$data = new Pimcore_Date();' . "\n";
               $code .= "\t\t" . '$this->set' . ucfirst($key) . '($data);' . "\n";
               $code .= "\t" . '}' . "\n";
           } else if ($this->getDefaultValue()) {

               $defaultValue = $this->getDefaultValue();
               if (!$defaultValue instanceof Pimcore_Date) {
                   $defaultValue = new Pimcore_Date($defaultValue);
               }

               $code .= "\t" . 'if(!$data) { ' . "\n";
               $code .= "\t\t" . '$data = new Pimcore_Date(' . $defaultValue->getTimestamp() . ');' . "\n";
               $code .= "\t\t" . '$this->set' . ucfirst($key) . '($data);' . "\n";
               $code .= "\t" . '}' . "\n";
           }


           $code .= "\t" . 'return $data;' . "\n";
           $code .= "}\n\n";

           return $code;
       }


       /**
        * Creates getter code which is used for generation of php file for object brick classes using this data type
        * @param $brickClass
        * @return string
        */
       public function getGetterCodeObjectbrick($brickClass)
       {
           $key = $this->getName();
           $code = '/**' . "\n";
           $code .= '* @return ' . $this->getPhpdocType() . "\n";
           $code .= '*/' . "\n";
           $code .= "public function get" . ucfirst($key) . " () {\n";

           $code .= "\t" . 'if(!$this->' . $key . ' && Object_Abstract::doGetInheritedValues($this->getObject())) {' . "\n";
           $code .= "\t\t" . 'return $this->getValueFromParent("' . $key . '");' . "\n";
           $code .= "\t" . '}' . "\n";


           if ($this->useCurrentDate) {
               $code .= "\t" . 'if(!' . '$this->' . $key . ') ' . '$this->' . $key . ' = new Pimcore_Date();' . "\n";
           } else if ($this->getDefaultValue()) {
               $defaultValue = $this->getDefaultValue();
               if (!$defaultValue instanceof Pimcore_Date) {
                   $defaultValue = new Pimcore_Date($defaultValue);
               }
               $code .= "\t" . 'if(!' . '$this->' . $key . ') ' . '$this->' . $key . ' = new Pimcore_Date(' . $defaultValue->getTimestamp() . ');' . "\n";
           }

           $code .= "\t" . 'return $this->' . $key . ";\n";


           $code .= "}\n\n";

           return $code;

       }

       /**
        * Creates getter code which is used for generation of php file for fieldcollectionk classes using this data type
        * @param $fieldcollectionDefinition
        * @return string
        */
       public function getGetterCodeFieldcollection($fieldcollectionDefinition)
       {
           $key = $this->getName();
           $code = '/**' . "\n";
           $code .= '* @return ' . $this->getPhpdocType() . "\n";
           $code .= '*/' . "\n";
           $code .= "public function get" . ucfirst($key) . " () {\n";

           if ($this->useCurrentDate) {
               $code .= "\t" . 'if(!' . '$this->' . $key . ') ' . '$this->' . $key . ' = new Pimcore_Date();' . "\n";
           } else if ($this->getDefaultValue()) {
               $defaultValue = $this->getDefaultValue();
               if (!$defaultValue instanceof Pimcore_Date) {
                   $defaultValue = new Pimcore_Date($defaultValue);
               }
               $code .= "\t" . 'if(!' . '$this->' . $key . ') ' . '$this->' . $key . ' =  new Pimcore_Date(' . $defaultValue->getTimestamp() . ');' . "\n";
           }

           $code .= "\t" . 'return $this->' . $key . ";\n";

           $code .= "}\n\n";

           return $code;
       }


       /**
        * Creates getter code which is used for generation of php file for localized fields in classes using this data type
        * @param $class
        * @return string
        */
       public function getGetterCodeLocalizedfields($class)
       {
           $key = $this->getName();
           $code = '/**' . "\n";
           $code .= '* @return ' . $this->getPhpdocType() . "\n";
           $code .= '*/' . "\n";
           $code .= "public function get" . ucfirst($key) . ' ($language = null) {' . "\n";

           $code .= "\t" . '$data = $this->getLocalizedfields()->getLocalizedValue("' . $key . '", $language);' . "\n";

           // adds a hook preGetValue which can be defined in an extended class
           $code .= "\t" . '$preValue = $this->preGetValue("' . $key . '");' . " \n";
           $code .= "\t" . 'if($preValue !== null && !Pimcore::inAdmin()) { return $preValue;}' . "\n";

           if ($this->useCurrentDate) {
               $code .= "\t" . 'if(!$data) { ' . "\n";
               $code .= "\t\t" . '$data = new Pimcore_Date();' . "\n";
               $code .= "\t\t" . '$this->set' . ucfirst($key) . '($data,$language);' . "\n";
               $code .= "\t" . '}' . "\n";
           } else if ($this->getDefaultValue()) {

               $defaultValue = $this->getDefaultValue();
               if (!$defaultValue instanceof Pimcore_Date) {
                   $defaultValue = new Pimcore_Date($defaultValue);
               }

               $code .= "\t" . 'if(!$data) { ' . "\n";
               $code .= "\t\t" . '$data = new Pimcore_Date(' . $defaultValue->getTimestamp() . ');' . "\n";
               $code .= "\t\t" . '$this->set' . ucfirst($key) . '($data,$language);' . "\n";
               $code .= "\t" . '}' . "\n";
           }


           $code .= "\t" . 'return $data;' . "\n";
           $code .= "}\n\n";
           return $code;
       }


    /**
     * @param boolean $useCurrentDate
     */
    public function setUseCurrentDate($useCurrentDate)
    {
        $this->useCurrentDate = (bool)$useCurrentDate;
    }


}
