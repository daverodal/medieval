<?php
$oClass = new ReflectionClass('Wargame\Cnst');
$constants = $oClass->getConstants();

        ?>

<script>

    <?php foreach($constants as $k => $v){
                echo "const $k = $v;\n";
            }?>
    const topCrtJson = '{!!json_encode($topCrt)!!}';
    const unitsJson = '{!!json_encode($units)!!}';
    const Const_line21 = '<?php echo asset('js/rowHex.svg'); ?>';

    var flashMessages;

</script>