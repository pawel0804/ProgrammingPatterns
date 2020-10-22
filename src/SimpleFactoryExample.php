<?php
interface TheProduct
{

}

interface TheFactory
{
    public function createProduct(): TheProduct;
}

class SomeProduct implements TheProduct
{

}

class SomeFactory implements TheFactory
{
    public function createProduct(): TheProduct
    {
    }
}