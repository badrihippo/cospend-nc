<?php
script('spend', 'moment-timezone-with-data.min');
script('spend', 'kjua.min');
script('spend', 'spend');

//style('spend', 'style');
style('spend', 'fontawesome/css/all.min');
style('spend', 'pspend');

?>

<div id="app">
    <div id="app-content">
            <?php print_unescaped($this->inc('maincontent')); ?>
    </div>
</div>
