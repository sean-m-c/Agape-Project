<?php

/*
 * Provides search functionality for anywhere needed in site
 */
class Search extends CComponent {

    /*
     * Input is form data, expected to be $_POST after a search form is submitted
     */
    public function createQuery($post) {
        $condition=null; // WHERE part of query

        // Loops through the post data, adding to condition
        foreach($post as $table) {
            foreach($table as $field) {
                echo "Table: $table, Field: $field";
                //$condition .= '('.$table.'.'.$field.'= AND ';
            }
        }

        // Remove extra AND
        if(!empty($condition))
            $condition = substr($condition,0,-5);
        
        return $condition;
    }
   
}