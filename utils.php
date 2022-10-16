<?php
function setMessage(bool $type, string $message): string {
    
    $firstBalise = $type ? '<p class="suc">' : '<p class="err">';
    $secondBalise = "</p>";

    return $firstBalise . $message . $secondBalise;

}
