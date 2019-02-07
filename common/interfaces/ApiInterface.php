<?php

namespace common\interfaces;

interface ApiInterface {

    public function addUserParams(array $params);

    public function validate();

    public function getBalance();
}
