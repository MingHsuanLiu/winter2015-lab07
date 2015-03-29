<?php

/**
 * This is a "CMS" model for quotes, but with bogus hard-coded data.
 * This would be considered a "mock database" model.
 *
 * @author jim
 */
class Menu extends CI_Model {

    protected $xml = null;
    protected $patty_names = array();
    protected $patties = array();
    // add missing sauces
    protected $toppings = array();
    protected $sauces = array();
    protected $cheeses = array();

    // Constructor
    public function __construct() {
        parent::__construct();
        $this->xml = simplexml_load_file(DATAPATH . 'menu.xml');

        // build the list of patties - approach 1
        foreach ($this->xml->patties->patty as $patty) {
            $patty_names[(string) $patty['code']] = (string) $patty;
        }

        // build a full list of patties - approach 2
        foreach ($this->xml->patties->patty as $patty) {
            $record = new stdClass();
            $record->code = (string) $patty['code'];
            $record->name = (string) $patty;
            $record->price = (float) $patty['price'];
            $this->patties[$record->code] = $record;
        }

        foreach ($this->xml->cheeses->cheese as $patty) {
            $record = new stdClass();
            $record->code = (string) $patty['code'];
            $record->name = (string) $patty;
            $record->price = (float) $patty['price'];
            $this->cheeses[$record->code] = $record;
        }

        foreach ($this->xml->toppings->topping as $patty) {
            $record = new stdClass();
            $record->code = (string) $patty['code'];
            $record->name = (string) $patty;
            $record->price = (float) $patty['price'];
            $this->toppings[$record->code] = $record;
        }

        foreach ($this->xml->sauces->sauce as $patty) {
            $record = new stdClass();
            $record->code = (string) $patty['code'];
            $record->name = (string) $patty;
            $this->sauces[$record->code] = $record;
        }
    }

    // retrieve a list of patties, to populate a dropdown, for instance
    function patties() {
        return $this->patty_names;
    }

    // retrieve a patty record, perhaps for pricing
    function getPatty($code) {
        if (isset($this->patties[$code]))
            return $this->patties[$code];
        else
            return null;
    }

    // retrieve a list of patties, to populate a dropdown, for instance
    function toppings() {
        return $this->topping_names;
    }

    // retrieve a patty record, perhaps for pricing
    function getTopping($code) {
        if (isset($this->toppings[$code]))
            return $this->toppings[$code];
        else
            return null;
    }

    // retrieve a list of patties, to populate a dropdown, for instance
    function sauces() {
        return $this->sauce_names;
    }

    // retrieve a patty record, perhaps for pricing
    function getSauce($code) {
        if (isset($this->sauces[$code]))
            return $this->sauces[$code];
        else
            return null;
    }

    // retrieve a patty record, perhaps for pricing
    function getCheese($code) {
        if (isset($this->cheeses[$code]))
            return $this->cheeses[$code];
        else
            return null;
    }

}
