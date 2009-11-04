<?php
$this->dispatcher->connect('routing.load_configuration', array('cleverMediaLibraryRouting', 'listenToRoutingLoadConfigurationEvent'));
