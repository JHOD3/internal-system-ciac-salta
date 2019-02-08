<?php if (count($aMedicos) > 0 and $tipo and $filter): ?>
    <?php $i = 1; ?>
    <?php foreach ($aMedicos AS $rsM): ?>
        <div class="pro" id="tag<?=$i?>">
            <?=trim($rsM['saludo'])?> <?=upper(trim($rsM['apellidos']))?> <?=upper(trim($rsM['nombres']))?>
            <input name="search_pro[]" value="<?=trim($rsM['saludo'])?> <?=upper(trim($rsM['apellidos']))?> <?=upper(trim($rsM['nombres']))?>" type="hidden" />
        </div>
        <?php $i++; ?>
    <?php endforeach; ?>
<?php endif; ?>
