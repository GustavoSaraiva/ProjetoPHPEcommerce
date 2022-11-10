<?php

    namespace Ecommerce;

    class Model{
        private $values = [];


        public function __call($name, $args)
        {
            // TODO: Implement __call() method.

            $method = substr($name, 0 , 3);

            $fieldName = substr($name, 3, strlen($name));

            //if (in_array($fieldName, $this->fields)) {
                switch ($method) {
                    case"get":
                        return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
                        break;


                    case"set":
                        $this->values[$fieldName] = $args[0];
                        break;
                }
            //}
    }

        public function getValues(){
            return $this->values;
        }


        public function setValues($data){
            foreach ($data as $key => $value){
                $this->{"set".$key}($value);
            }
        }
    }
