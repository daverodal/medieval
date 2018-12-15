<script>
    const topCrtJson = '{!!json_encode($topCrt)!!}';
    const unitsJson = '{!!json_encode($units)!!}';
    const Const_line21 = '<?php echo asset('vendor/wargame/genre/images/rowHex.svg'); ?>';


    const addUrl = "<?=url("wargame/add/");?>";
    const pokeUrl = "<?=url("wargame/poke/");?>";
    if(!fetchUrl){
        const fetchUrl = "<?=url("wargame/fetch/$wargame");?>";
    }
    const wargame = "<?=$wargame?>";
    const rowSvg = "<?php echo asset('vendor/wargame/genre/images/rowHex.svg');?>";
    const imagesBase = "<?php echo asset('vendor/wargame/genre/images/');?>";

    const mapSymbolsBefore = "<?php echo asset('js');?>/";
    const mapSymbolsSpotted = "<?php echo asset('vendor/wargame/tactical/images/spotted.svg');?>";

</script>