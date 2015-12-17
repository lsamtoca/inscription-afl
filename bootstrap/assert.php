<?php

// Activation des assertions et mise en mode discret
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

// Création du gestionnaire d'assertions
function my_assert_handler($file, $line, $code) {
    global $testing;
    if ($testing) {
        echo "<hr>Assertion failure:
        File: $file<br />
        Line: $line<br />
        Code: $code<br /><hr />";
    } else {
        xhtml_pre("Server internal misconfiguration");
        xhtml_post();
        exit;
    }
}

// Configuration de la méthode de callback
assert_options(ASSERT_CALLBACK, 'my_assert_handler');
