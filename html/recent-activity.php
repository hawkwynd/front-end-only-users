	<div class="OptionTab ActiveTab" id="EditProduct">
		<div id='col-right'>
			<div class='col-wrap'>
				<div class="tablenav top">
					<div class="alignleft actions">
						<p><?php _e("Recent User Activity disabled", 'EWD_FEUP'); ?>
					</div>
					<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
						<span class="displaying-num"><?php echo $EventCount; ?> <?php _e("events", 'EWD_FEUP') ?> <?php if ($EWD_FEUP_Full_Version != "Yes") {echo "out of 100";}?></span>
						<span class='pagination-links'>
							<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='Go to the first page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=1'>&laquo;</a>
							<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='Go to the previous page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page-1;?>'>&lsaquo;</a>
							<span class="paging-input"><?php echo $Page; ?> <?php _e("of", 'EWD_FEUP') ?> <span class='total-pages'><?php echo $Number_of_Pages; ?></span></span>
							<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='Go to the next page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page+1;?>'>&rsaquo;</a>
							<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='Go to the last page' href='<?php echo $Current_Page_With_Order_By . "&Page=" . $Number_of_Pages; ?>'>&raquo;</a>
						</span>
					</div>
				</div>
				
				<table class="wp-list-table widefat tags sorttable fields-list ui-sortable" cellspacing="0">
					<thead>
						<tr>
							<th scope='col' class='manage-column column-cb check-column'  style="">
								<?php if ($_GET['OrderBy'] == "Event_Type" and $_GET['Order'] == "ASC") {$Order = "DESC";}
else {$Order = "ASC";} ?>
								<a href="<?php echo $Current_Page; ?>&OrderBy=Event_Type&Order=<?php echo $Order; ?>">
								<span>Event Type</span>
								<span class="sorting-indicator"></span>
							</th>
							<th scope='col' class='manage-column column-cb check-column'  style="">
								<?php if ($_GET['OrderBy'] == "Event_Location_Title" and $_GET['Order'] == "ASC") {$Order = "DESC";}
else {$Order = "ASC";} ?>
								<a href="<?php echo $Current_Page; ?>&OrderBy=Event_Location_Title&Order=<?php echo $Order; ?>">
								<span>Event Location</span>
								<span class="sorting-indicator"></span>
							</th>
							<th scope='col' class='manage-column column-cb check-column'  style="">
								<?php if ($_GET['OrderBy'] == "Event_Target_Title" and $_GET['Order'] == "ASC") {$Order = "DESC";}
else {$Order = "ASC";} ?>
								<a href="<?php echo $Current_Page; ?>&OrderBy=Event_Target_Title&Order=<?php echo $Order; ?>">
								<span>Event Target Title</span>
								<span class="sorting-indicator"></span>
							</th>
							<th scope='col' class='manage-column column-cb check-column'  style="">
								<?php if ($_GET['OrderBy'] == "Event_Date" and $_GET['Order'] == "ASC") {$Order = "DESC";}
else {$Order = "ASC";} ?>
								<a href="<?php echo $Current_Page; ?>&OrderBy=Event_Date&Order=<?php echo $Order; ?>">
								<span>Event Date</span>
								<span class="sorting-indicator"></span>
							</th>
						</tr>
					</thead>
				
					<tfoot>
						<tr>
							<th scope='col' class='manage-column column-cb check-column'  style="">
								<?php if ($_GET['OrderBy'] == "Event_Type" and $_GET['Order'] == "ASC") {$Order = "DESC";}
else {$Order = "ASC";} ?>
								<a href="<?php echo $Current_Page; ?>&OrderBy=Event_Type&Order=<?php echo $Order; ?>">
								<span>Event Type</span>
								<span class="sorting-indicator"></span>
							</th>
							<th scope='col' class='manage-column column-cb check-column'  style="">
								<?php if ($_GET['OrderBy'] == "Event_Location_Title" and $_GET['Order'] == "ASC") {$Order = "DESC";}
else {$Order = "ASC";} ?>
								<a href="<?php echo $Current_Page; ?>&OrderBy=Event_Location_Title&Order=<?php echo $Order; ?>">
								<span>Event Location</span>
								<span class="sorting-indicator"></span>
							</th>
							<th scope='col' class='manage-column column-cb check-column'  style="">
								<?php if ($_GET['OrderBy'] == "Event_Target_Title" and $_GET['Order'] == "ASC") {$Order = "DESC";}
else {$Order = "ASC";} ?>
								<a href="<?php echo $Current_Page; ?>&OrderBy=Event_Target_Title&Order=<?php echo $Order; ?>">
								<span>Event Target Title</span>
								<span class="sorting-indicator"></span>
							</th>
							<th scope='col' class='manage-column column-cb check-column'  style="">
								<?php if ($_GET['OrderBy'] == "Event_Date" and $_GET['Order'] == "ASC") {$Order = "DESC";}
else {$Order = "ASC";} ?>
								<a href="<?php echo $Current_Page; ?>&OrderBy=Event_Date&Order=<?php echo $Order; ?>">
								<span>Event Date</span>
								<span class="sorting-indicator"></span>
							</th>
						</tr>
					</tfoot>
				
					<tbody id="the-list" class='list:tag'>
					<?php if ($EWD_FEUP_Full_Version == "Yes") { ?>					
					<?php
    if ($myrows) {
        foreach ($myrows as $Event) {
            echo "<tr id='Event-" . $Event->User_Event_ID ."'>";
            echo "<td class='name column-type'>" .  $Event->Event_Type . "</td>";
            echo "<td class='name column-location'>" .  $Event->Event_Location_Title . "</td>";
            echo "<td class='name column-target'>" .  $Event->Event_Target_Title . "</td>";
            echo "<td class='name column-date'>" .  $Event->Event_Date . "</td>";
            echo "</tr>";
        }
    }
    ?>
					<?php } else {
    echo "<tr>";
    echo "<td colspan='4'>";
    echo __("The full version of Front-End Only Users is required to view user activity.", "EWD_FEUP");
    echo "<a href='http://www.etoilewebdesign.com/front-end-users-plugin/'>";
    echo __(" Please upgrade to view this information!", 'EWD_FEUP');
    echo "</a>";
    echo "</td>";
    echo "</tr>";
} ?>
				
					</tbody>
				</table>
				
				<div class="tablenav bottom">
					<?php /*<div class="alignleft actions">
						<select name='action'>
				  			<option value='-1' selected='selected'><?php _e("Bulk Actions", 'EWD_FEUP') ?></option>
							<option value='delete'><?php _e("Delete", 'EWD_FEUP') ?></option>
						</select>
						<input type="submit" name="" id="doaction" class="button-secondary action" value="<?php _e('Apply', 'EWD_FEUP') ?>"  />
					</div>*/ ?>
					<div class='tablenav-pages <?php if ($Number_of_Pages == 1) {echo "one-page";} ?>'>
						<span class="displaying-num"><?php echo $EventCount; ?> <?php _e("events", 'EWD_FEUP') ?></span>
						<span class='pagination-links'>
							<a class='first-page <?php if ($Page == 1) {echo "disabled";} ?>' title='Go to the first page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=1'>&laquo;</a>
							<a class='prev-page <?php if ($Page <= 1) {echo "disabled";} ?>' title='Go to the previous page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page-1;?>'>&lsaquo;</a>
							<span class="paging-input"><?php echo $Page; ?> <?php _e("of", 'EWD_FEUP') ?> <span class='total-pages'><?php echo $Number_of_Pages; ?></span></span>
							<a class='next-page <?php if ($Page >= $Number_of_Pages) {echo "disabled";} ?>' title='Go to the next page' href='<?php echo $Current_Page_With_Order_By; ?>&Page=<?php echo $Page+1;?>'>&rsaquo;</a>
							<a class='last-page <?php if ($Page == $Number_of_Pages) {echo "disabled";} ?>' title='Go to the last page' href='<?php echo $Current_Page_With_Order_By . "&Page=" . $Number_of_Pages; ?>'>&raquo;</a>
						</span>
					</div>
					<br class="clear" />
				</div>

			</div>
		</div>