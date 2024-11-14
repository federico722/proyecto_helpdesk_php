<?php

require_once '../../includes/Role.class.php';

   if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['role'])) {
       Role::create_role($_GET['role']);
   }
