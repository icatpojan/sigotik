<!-- BEGIN SIDEBAR MENU -->
<ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
    <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
    <li class="sidebar-toggler-wrapper hide">
        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
        <div class="sidebar-toggler">
            <span></span>
        </div>
        <!-- END SIDEBAR TOGGLER BUTTON -->
    </li>
    <li class="nav-item active" data-source="dashboard">
        <a href="#" onClick="window.location.reload();return false;" class="nav-link nav-toggle">
            <i class="fa fa-institution"></i>
            <span class="title">Dashboard</span>
        </a>
    </li>
	<?php 
	// var_dump(VIEWPATH);die;
	$sql = $this->db->query("	SELECT conf_role_menu.id, 
								stm_menuv2.id, 
								stm_menuv2.id_parentmenu, 
								stm_menuv2.menu, 
								stm_menuv2.linka,
								stm_menuv2.icon,
								stm_menuv2.`level`,
								stm_menuv2.urutan
								FROM `conf_role_menu`
								JOIN stm_menuv2 AS menuchild ON menuchild.id = conf_role_menu.stm_menu_id
								LEFT JOIN stm_menuv2 ON (menuchild.id_parentmenu = stm_menuv2.id OR stm_menuv2.id = conf_role_menu.stm_menu_id)
								WHERE conf_role_menu.conf_group_id = '".$this->session->userdata('conf_group_id')."' AND stm_menuv2.`level` = '1' GROUP BY urutan ORDER BY urutan ASC");
	foreach($sql->result() AS $list){
			if($list->id_parentmenu == NULL && $list->linka == NULL){
				$data = 'class="sub-menu" style="display: none;"';
				$arrow = '<span class="arrow open"></span>';
			}else{
				$data = '';
				$arrow = '';
			}
	?>
	<li class="nav-item" data-source="<?php echo $list->linka; ?>">
		<a href="#" class="nav-link nav-toggle">
			<i class="<?php echo $list->icon; ?>"></i>
			<span class="title"><?php echo $list->menu; ?></span>
			<?php echo $arrow; ?>
		</a>
			<ul <?php echo $data;?>>
		<?php 
		$sql2 = $this->db->query("	SELECT conf_role_menu.id, 
									menuchild.id_parentmenu,
									menuchild.menu, 
									menuchild.linka,
									menuchild.`level`,
									menuchild.urutan
									FROM `conf_role_menu`
									JOIN stm_menuv2 AS menuchild ON menuchild.id = conf_role_menu.stm_menu_id
									WHERE conf_role_menu.conf_group_id = '".$this->session->userdata('conf_group_id')."' AND id_parentmenu = '".$list->id."' GROUP BY urutan ORDER BY urutan ASC");
		foreach($sql2->result() AS $list2){
		?>	
			<li class="nav-item" data-source="<?php echo $list2->linka?>">
				<a href="#" class="nav-link">
					<span class="title"><?php echo $list2->menu; ?></span>
				</a>
			</li>
			
		<?php	
		}
		?>
			</ul>
	</li>
	<?php
	}
	
	?>
	<!--<li class="nav-item" data-source="">
		<a href="#" class="nav-link nav-toggle">
			<i class="fa fa-user-plus"></i>
			<span class="title">ConfigSSSS</span>
			<span class="arrow open"></span>
		</a>
		<ul class="sub-menu" style="display: none;">
			<li class="nav-item" data-source="">
				<a href="#" class="nav-link nav-toggle">
					<span class="title">User</span>
					<span class="arrow open"></span>
				</a>
				<ul class="sub-menu" style="display: none;">
					<li class="nav-item" data-source="config.display_user">
						<a href="#" class="nav-link">
							<span class="title">User</span>
						</a>
					</li>
					<li class="nav-item" data-source="config.display_role">
						<a href="#" class="nav-link">
							<span class="title">Role</span>
						</a>
					</li>
				</ul>
			</li>
			<li class="nav-item" data-source="config.display_role">
				<a href="#" class="nav-link">
					<span class="title">Role</span>
				</a>
			</li>
		</ul>
	</li>
	
	<!--<li class="nav-item" data-source="">
		<a href="#" class="nav-link nav-toggle">
			<i class="fa fa-archive"></i>
			<span class="title"> Master Data</span>
			<span class="arrow open"></span>
		</a>
		<ul class="sub-menu" style="display: none;">
			<li class="nav-item" data-source="master.display_kapal">
				<a href="#" class="nav-link">
					<span class="title">Kapal</span>
				</a>
			</li>
			<li class="nav-item" data-source="master.display_upt">
				<a href="#" class="nav-link">
					<span class="title">UPT</span>
				</a>
			</li>
		</ul>
	</li>-->
	
	

<!--    <li class="nav-item" data-source="message.inbox_message">
        <a href="#" class="nav-link nav-toggle">
            <i class="fa fa-inbox"></i>
            <span class="title">Inbox</span>
        </a>
    </li>

    <li class="nav-item" data-source="outbox">
        <a href="#" class="nav-link nav-toggle">
            <i class="fa fa-envelope-o"></i>
            <span class="title">Outbox</span>
        </a>
    </li> 
-->


</ul>
<!-- END SIDEBAR MENU -->