<?php
/**
 * Render functions for various HTML components.
 * Wraps `include()` statements allowing easy reuse of HTML components.
 * The $data variable becomes available to the content inside of the included php file.
 */


function render_manga_card($data = [])
{
    include(__DIR__ . "/../partials/manga_card.php");
}

function render_input($data = [])
{
    include(__DIR__ . "/../partials/input_field.php");
}

function render_button($data = [])
{
    include(__DIR__ . "/../partials/button.php");
}
function render_table($data =[])
{
    include(__DIR__."/../partials/table.php");
}