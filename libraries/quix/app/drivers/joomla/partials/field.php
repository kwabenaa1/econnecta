<?php

/**
 * Get field value by the given array keys.
 * 
 * @param string $path
 */
function field($path = null) {
  
  // global field
  global $field;

  // if path won't sent, returns whole field values
  if($path == null) return $field;
  
  // init array keys from the given path
  $array_keys = explode('.', $path);
  
  $data = null;

  // getting field value from field array
  $getValue = function($data, $key) {
    foreach($data as $field) {
      if($field['name'] == $key) return $field;
    }

    throw new Exception("Field [ {$key} ] doesn't exists");
  };

  // looping into array for getting value for the given key
  foreach($array_keys as $key) {
    $data = $data == null? $field[$key] : $getValue($data, $key);
  }


  // if value exists in the data then returns value
  // if value doesn't exists in this case returns whole data
  return isset($data['value'])? $data['value'] : $data;
}