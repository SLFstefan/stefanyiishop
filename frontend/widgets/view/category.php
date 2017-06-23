<?php
use yii\helpers\Html;
foreach($categories as $key=>$category):?>
    <div class="cat <?=$key==0?'item1':''?>">
        <h3><?=Html::a($category->name,['member/list','cate_id'=>$category->id])?><b></b></h3>
        <div class="cat_detail">
            <?php foreach ($category->children as $key2=>$children):?>
                <dl <?php $key2==0?'class="dl_1st"':''?>>
                    <dt><?=Html::a($children->name,['member/list','cate_id'=>$children->id])?></dt>
                    <dd>
                        <?php foreach ($children->children as $child):?>
                            <?=Html::a($child->name,['member/list','cate_id'=>$child->id])?>
                        <?php endforeach;?>
                    </dd>
                </dl>
            <?php endforeach;?>
        </div>
    </div>
<?php endforeach;?>