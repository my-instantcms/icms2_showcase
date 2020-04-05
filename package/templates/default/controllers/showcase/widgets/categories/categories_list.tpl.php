<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/libs/mmenu/jquery.mmenu.all.css', false));
	$this->addJS($this->getTplFilePath('controllers/showcase/libs/mmenu/jquery.mmenu.all.js', false));
?>
<nav id="sc_cats_<?php html($widget->id); ?>" class="SC_wd_cats">

    <ul>

        <?php $last_level = 0; $is_visible = false; $show_full_tree = $widget->getOption('show_full_tree'); ?>

        <?php foreach($cats as $id=>$item){ ?>

            <?php
                $is_active = (!empty($active_cat['id']) && $id == $active_cat['id']);
                $is_visible = isset($path[$item['id']]) || isset($path[$item['parent_id']]) || $item['ns_level'] <= 1;
                if (!isset($item['ns_level'])) { $item['ns_level'] = 1; }
                $item['childs_count'] = ($item['ns_right'] - $item['ns_left']) > 1;
                $font_icon = !empty($item['sc_fa']) ? $item['sc_fa'] : 'fa-file-text';
				$url = href_to($ctype_name, $item['slug']);
            ?>

            <?php for ($i=0; $i<($last_level - $item['ns_level']); $i++) { ?>
                </li></ul>
            <?php } ?>

            <?php if ($item['ns_level'] <= $last_level) { ?>
                </li>
            <?php } ?>

            <?php
                $css_classes = array();
                if ($is_active) { $css_classes[] = 'Selected'; }
                if ($item['childs_count']) { $css_classes[] = 'folder'; }
                if (!$is_visible && !$show_full_tree) { $css_classes[] = 'folder_hidden'; }
            ?>

            <li <?php if ($css_classes) { ?>class="<?php echo implode(' ', $css_classes); ?>"<?php } ?>>
			
				<?php /*if ($item['childs_count']) { ?><span><?php } else { ?><a href="<?php echo $url; ?>"><?php } ?>
					<i class="fa <?php html($font_icon); ?>"></i> 
                    <?php html($item['title']); ?>
				<?php if ($item['childs_count']) { ?></span><?php } else { ?></a><?php } */ ?>

                <a href="<?php echo $url; ?>">
					<i class="fa <?php html($font_icon); ?>"></i> 
					<?php /* ?><img src="/templates/default/controllers/showcase/img/icons/<?php html($item['sc_icon']); ?>" /> <?php */ ?>
					<?php if (!empty($item['goods_count'])){ ?>
						<span class="mm-counter"><?php html($item['goods_count']); ?></span>
					<?php } ?>
                    <?php html($item['title']); ?>
				</a>

                <?php if ($item['childs_count']) { ?><ul><?php } ?>

                <?php $last_level = $item['ns_level']; ?>

        <?php } ?>

        <?php for ($i=0; $i<$last_level; $i++) { ?>
            </li></ul>
        <?php } ?>

</nav>
<style>
	.SC_wd_cats{background:#fff;margin-bottom: 10px;}
	.SC_wd_cats > ul:before{content:'';height: 40px;display: block;background: -webkit-linear-gradient(#f4f4f4, #e2e2e2);background: linear-gradient(#f4f4f4, #e2e2e2);}
	.SC_wd_cats > ul{background:#fff;border: 1px solid #ddd;}
	.SC_wd_cats > ul li{height:40px;line-height:40px;padding: 0 10px;}
	.SC_wd_cats > ul li a{color:#444;text-decoration:none}
	.SC_wd_cats .mm-panels>.mm-panel>.mm-listview{margin-bottom:0}
	.SC_wd_cats .mm-panels > .mm-panel.mm-opened{border: 1px solid #ccc;}
	.SC_wd_cats .mm-panels > .mm-panel:after{height: 0;}
	.SC_wd_cats .mm-listview>li:last-child:after{content:none}
	.SC_wd_cats .mm-navbar{
		background: -webkit-linear-gradient(#f4f4f4, #e2e2e2);
		background: linear-gradient(#f4f4f4, #e2e2e2);
	}
	.SC_wd_cats .mm-navbar .mm-title .fa{margin-left: -10px;padding-right: 9px;}
	.SC_wd_cats .mm-navbar .mm-title {color: #696969;font-size: 18px;letter-spacing: 1px;}
	.SC_wd_cats .mm-listview>li>a.mm-fullsubopen:hover+span,.SC_wd_cats .mm-listview>li>a:not(.mm-fullsubopen):hover {
		background: rgba(235, 235, 235, 0.5)
	}
	.SC_wd_cats .mm-listview>li.mm-selected>a:hover+span,.SC_wd_cats .mm-listview>li.mm-selected>a{
		background: rgba(235, 235, 235, 0.5)
	}
	.SC_wd_cats .mm-listview>li>a,.SC_wd_cats .mm-listview>li>span{padding-left:12px}
	.SC_wd_cats .mm-listview>li>a.mm-next{padding-left:0}
	.SC_wd_cats li .fa{
		margin:0 15px 0 0;
		font-size: 16px;
		width: 12px;
		text-align: center;
	}
	.SC_wd_cats select{display:none}
	.SC_wd_cats .mm-counter{
		color: rgba(0,0,0,.3);
		text-align: right;
		display: block;
		min-width: 30px;
		float: right;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {

		$("#sc_cats_<?php html($widget->id); ?>").mmenu({
			extensions: ["iconbar", "border-full"],
			autoHeight: true,
			offCanvas: false,
			slidingSubmenus: true,
			navbar: {
				"title": '<i class="fa fa-folder-open-o"></i> <?php html($widget->title); ?>'
			},
		});

	});
</script>