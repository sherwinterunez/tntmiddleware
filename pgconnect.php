<?php

$conn = pg_connect("host=127.0.0.1 port=5432 dbname=sherwint_sms101 user=sherwint_sherwin password=joshua04");

print_r(array('$conn'=>$conn));