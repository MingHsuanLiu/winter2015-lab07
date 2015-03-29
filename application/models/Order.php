<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Order extends MY_Model
{
    public function makeOrder($xmlDoc)
    {
        $xml = simplexml_load_file(DATAPATH . $xmlDoc);
        $this->load->model('Menu');

        $record = array();
        $record['customer'] = (String)$xml->customer;
        $record['eatin'] = (String)$xml->attributes()['type'];
        $record['instructions'] = isset($xml->instructions) ?
                                    (String)($xml->instructions) :
                                    'none';
        $burgerMl = $xml['burger'];

        $total = 0;
        $burgers = array();
        $count = 1;

        foreach ($xml->burger as $disBurger)
        {
            $subtotal = 0;

            // Setup the one to many relations within the burger
            $burger = array( 'special'  => 'none',
                             'cheeses'  => 'none',
                             'toppings' => 'none',
                               'sauces' => 'none');
            if (isset($disBurger->cheeses))
            {
                $bar = array_values((array)($disBurger->cheeses->attributes()));
                $bar = (array_values($bar[0])); // this is terrible
                $cheesea = array();
                // add cheeses
                foreach ($bar as $cheeseCode)
                    $cheesea[] = $this->Menu->GetCheese($cheeseCode)->name;
                $burger['cheeses'] = $this->commaSeparatedStringList($cheesea);

                // charge for cheeses
                foreach ($bar as $cheese)
                    $subtotal += $this->Menu->getCheese($cheese)->price;
            }

            if (isset($disBurger->topping))
            {
                $toppings = array();
                foreach ($disBurger->topping as $topping)
                {
                    // add topping
                    $topping = (String)$topping['type'];
                    $toppings[] = $this->Menu->getTopping($topping)->name;
                    // charge for topping
                    if (isset($this->Menu->getTopping($topping)->price))
                        $subtotal += $this->Menu->getTopping($topping)->price;
                }

                $burger['toppings'] = $this->commaSeparatedStringList
                (
                    $toppings
                );
            }

            if (isset($disBurger->sauce))
            {
                $sauces = array();
                foreach ($disBurger->sauce as $sauce)
                {
                    $sauce = (String)$sauce['type'];
                    // add sauce
                    $sauces[] = $this->Menu->getSauce($sauce)->name;

                    // cbarge for sayce
                    if (isset($this->Menu->getSauce($sauce)->price))
                        $subtotal += $this->Menu->getSauce($sauce)->price;
                }

                $burger['sauces'] = $this->commaSeparatedStringList
                (
                    $sauces
                );
            }

            // one to one next
            $burger['header'] = (isset($disBurger->name))?
                                    (String)($disBurger->name) :
                                    "burger #" . $count++;
            $patty = (String)($disBurger->patty['type']);


            // add paty price and name
            $burger['base'] = $this->Menu->getPatty($patty)->name;
            $subtotal += $this->Menu->getPatty($patty)->price;

            // add instructions
            if (isset($disBurger->instructions))
                $burger['instructions'] = (String)($disBurger->instructions);

            $total += $subtotal;
            $burger['subtotal'] = $subtotal;
            $burgers[] = $burger;

        }

        $record['burgers'] = $burgers;
        $record['total'] = $total;

        return $record;
    }

    private function commaSeparatedStringList($arr)
    {
        $output = "";

        if (isset($arr) && count($arr) > 0)
            $output = $arr[0];

        for ($i = 1; $i < count($arr); $i++)
            $output .= ', ' . $arr[$i];

        return $output;
    }
}