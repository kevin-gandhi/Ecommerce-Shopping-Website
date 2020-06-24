<?php
namespace WbsVendors\Dgm\Shengine\Interfaces;


interface IRate
{
    function getCost();
    function getTitle();
    function isTaxable();
}