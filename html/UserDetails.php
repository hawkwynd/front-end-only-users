<?php
$Admin_Approval = get_option("EWD_FEUP_Admin_Approval");
$Email_Confirmation = get_option("EWD_FEUP_Email_Confirmation");
$Payment_Frequency = get_option("EWD_FEUP_Payment_Frequency");
?>
<!-- The details of a specific user for editing, based on the user ID -->
<?php

$UserDetails = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_EWD_FEUP_User_Fields WHERE User_ID ='%d'", $_GET['User_ID']));
$UserAdmin = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ewd_feup_user_table_name WHERE User_ID ='%d'", $_GET['User_ID']));

$UserProperName = $wpdb->get_row( $wpdb->prepare("SELECT * FROM wp_EWD_FEUP_User_Fields WHERE User_ID='%d' AND Field_Name LIKE 'Name'", $_GET['User_ID'] ) );

$Levels = $wpdb->get_results("SELECT * FROM $ewd_feup_levels_table_name ORDER BY Level_Privilege ASC");

if (isset($_GET['Page'])) {
    $Page = $_GET['Page'];
} else {
    $Page = 1;
}

// flush this crap from the database where possible.. 14769
# $rows_del = $wpdb->delete('wp_EWD_FEUP_User_Events', 1);

// Fix this crap
# $updated_rows = $wpdb->update('wp_EWD_FEUP_Fields', array('Field_Options' =>'YES,NO'), array('Field_Type' => 'Radio'));

$Current_Page = "admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_User_Details&Selected=User&User_ID=" . $UserDetails[0]->User_ID;

/*
		$Sql = "SELECT * FROM $ewd_feup_user_events_table_name ";
		$Sql .= "WHERE User_ID=%d ";
		if (isset($_GET['OrderBy'])) {$Sql .= "ORDER BY " . $_GET['OrderBy'] . " " . $_GET['Order'] . " ";}
		else {$Sql .= "ORDER BY Event_Date DESC ";}
		$Sql .= "LIMIT " . ($Page - 1)*100 . ",100";
		$myrows = $wpdb->get_results($wpdb->prepare($Sql, $_GET['User_ID']));
		$Number_of_Pages = ceil($wpdb->num_rows/100);
		if (isset($_GET['OrderBy'])) {$Current_Page_With_Order_By = $Current_Page . "&OrderBy=" .$_GET['OrderBy'] . "&Order=" . $_GET['Order'];}
		$EventCount = $wpdb->num_rows;
        include('recent-activity.php');
*/
?>

<div id='col-left'>
    <div class='col-wrap'>
        <div class="form-wrap UserDetail">
            <a href="admin.php?page=EWD-FEUP-options&DisplayPage=Users" class="NoUnderline">
                &#171; <?php _e("Back", 'EWD_FEUP') ?></a>

            <h2><?php _e("Edit User", 'EWD_FEUP'); ?>: <?php echo($UserProperName->Field_Value); ?></h2>
            <a class='delete-tag feup-confirm-one-user'
               href='admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_DeleteUser&DisplayPage=Users&User_ID=<?php echo $UserAdmin->User_ID; ?>'><?php _e("Delete User", 'EWD_FEUP') ?></a>

            <p><?php echo __("Member since ", 'EWD_FEUP') . $UserAdmin->User_Date_Created; ?></p>


            <?php $Fields = $wpdb->get_results("SELECT * FROM $ewd_feup_fields_table_name"); ?>


            <!-- Form to update a user -->
            <form id="addtag" method="post"
                  action="admin.php?page=EWD-FEUP-options&Action=EWD_FEUP_EditUser&DisplayPage=Users" class="validate"
                  enctype="multipart/form-data">
                <input type="hidden" name="action" value="Edit_User"/>
                <input type="hidden" name="User_ID" value="<?php echo $_GET['User_ID']; ?>"/>
                <?php wp_nonce_field(); ?>
                <?php wp_referer_field(); ?>
                <select name='Level_ID'>
                    <option value='0'>None (0)</option>
                    <?php foreach ($Levels as $Level) {
                        echo "<option value='" . $Level->Level_ID . "' ";
                        if ($UserAdmin->Level_ID == $Level->Level_ID) {
                            echo "selected=selected";
                        }
                        echo ">" . $Level->Level_Name . " (" . $Level->Level_Privilege . ")</option>";
                    }?>
                </select>
                <?php if ($Admin_Approval == "Yes") { ?>
                    <label for='Admin Approved' id='ewd-feup-register-admin-approved-div'
                           class='ewd-feup-field-label'><?php _e('Admin Approved', 'EWD_FEUP'); ?>: </label>
                    <input type='radio' class='ewd-feup-text-input' name='Admin_Approved'
                           value='Yes' <?php if ($UserAdmin->User_Admin_Approved == "Yes") {
                        echo "checked";
                    } ?>>Yes<br/>
                    <input type='radio' class='ewd-feup-text-input' name='Admin_Approved'
                           value='No' <?php if ($UserAdmin->User_Admin_Approved == "No") {
                        echo "checked";
                    } ?>>No<br/>
                <?php } ?>
                <?php if ($Email_Confirmation == "Yes") { ?>
                    <label for='Email Confirmation' id='ewd-feup-register-admin-approved-div'
                           class='ewd-feup-field-label'><?php _e('Email Confirmed', 'EWD_FEUP'); ?>: </label>
                    <input type='radio' class='ewd-feup-text-input' name='Email_Confirmation'
                           value='Yes' <?php if ($UserAdmin->User_Email_Confirmed == "Yes") {
                        echo "checked";
                    } ?>>Yes<br/>
                    <input type='radio' class='ewd-feup-text-input' name='Email_Confirmation'
                           value='No' <?php if ($UserAdmin->User_Email_Confirmed == "No") {
                        echo "checked";
                    } ?>>No<br/>
                <?php } ?>
                <?php if ($Payment_Frequency != "None") { ?>
                    <label for='User Membership Fees Paid' id='ewd-feup-register-admin-approved-div'
                           class='ewd-feup-field-label'><?php _e('Membership Fees Paid', 'EWD_FEUP'); ?>: </label>
                    <input type='radio' class='ewd-feup-text-input' name='User_Membership_Fees_Paid'
                           value='Yes' <?php if ($UserAdmin->User_Membership_Fees_Paid == "Yes") {
                        echo "checked";
                    } ?>>Yes<br/>
                    <input type='radio' class='ewd-feup-text-input' name='User_Membership_Fees_Paid'
                           value='No' <?php if ($UserAdmin->User_Membership_Fees_Paid == "No") {
                        echo "checked";
                    } ?>>No<br/>
                <?php } ?>
                <?php if ($Payment_Frequency == "Yearly" or $Payment_Frequency == "Monthly") { ?>
                    <label for='User Membership Fees Paid' id='ewd-feup-register-admin-approved-div'
                           class='ewd-feup-field-label'><?php _e('Account Expiry Date', 'EWD_FEUP'); ?>: </label>
                    <input type='datetime-local' class='ewd-feup-text-input' name='User_Account_Expiry'
                           value='<?php echo str_replace(" ", "T", $UserAdmin->User_Account_Expiry); ?>'>
                <?php } ?>



                <?php foreach ($Fields as $Field) {
                    $Value = "";
                    foreach ($UserDetails as $UserField) {
                        if ($Field->Field_Name == $UserField->Field_Name) {
                            $Value = $UserField->Field_Value;
                        }
                    }
                    ?>
                    <div class="form-field form-required">
                        <?php if ($Field->Field_Type != "radio") { // moved to right column, dont display radios here
                            echo "<label for='" . $Field->Field_Name . "'>" . $Field->Field_Name . "</label>";
                        }
                        ?>
                        <?php if ($Field->Field_Type == "text" or $Field->Field_Type == "mediumint") { ?><input
                            name="<?php echo $Field->Field_Name; ?>" class='ewd-admin-regular-text'
                            id="<?php echo $Field->Field_Name; ?>" type="text" value="<?php echo $Value; ?>"
                            size="60" />
                        <?php } elseif ($Field->Field_Type == "date") { ?>
                            <input name='<?php echo $Field->Field_Name; ?>'
                                   id='ewd-feup-register-input-<?php echo $Field->Field_ID; ?>'
                                   class='ewd-feup-date-input pure-input-1-3' type='date'
                                   value='<?php echo $Value; ?>'/>
                        <?php } elseif ($Field->Field_Type == "datetime") { ?>
                            <input name='<?php echo $Field->Field_Name; ?>'
                                   id='ewd-feup-register-input-<?php echo $Field->Field_ID; ?>'
                                   class='ewd-feup-datetime-input pure-input-1-3' type='datetime-local'
                                   value='<?php echo $Value; ?>'/>
                        <?php } elseif ($Field->Field_Type == "textarea") { ?>
                            <textarea name="<?php echo $Field->Field_Name; ?>" class='ewd-admin-large-text'
                                      id="<?php echo $Field->Field_Name; ?>"><?php echo $Value ?></textarea>
                        <?php } elseif ($Field->Field_Type == "file") { ?>
                            <?php echo __("Current file:", 'EWD_FEUP') . " " . substr($Value, 10); ?>
                            <input name='<?php echo $Field->Field_Name; ?>'
                                   id='ewd-feup-register-input-<?php echo $Field->Field_ID; ?>'
                                   class='ewd-feup-date-input pure-input-1-3' type='file' value=''/>
                        <?php } elseif ($Field->Field_Type == "picture") { ?>
                            <?php
                                // if we have a picture, then display it else dont. -hawkwynd

                            if($Value !=""){
                                _e("Current Picture - ", 'EWD_FEUP');
                                echo "<img src='" . site_url("/wp-content/uploads/ewd-feup-user-uploads/").$Value .
                                    "' alt='" . $Field->Field_Name . "' class='ewd-feup-profile-picture' /><br/>";
                            }
                                echo "<input name='" . $Field->Field_Name . "' class='ewd-feup-file-input' type='file' value=''/>";
                            ?>
                        <?php } elseif ($Field->Field_Type == "select" or $Field->Field_Type == "countries") { ?>
                            <?php $Options = explode(",", $Field->Field_Options); ?>
                            <?php if ($Field->Field_Type == "countries") {
                                $Options = EWD_FEUP_Return_Country_Array();
                            } ?>
                            <select name="<?php echo $Field->Field_Name; ?>" id="<?php echo $Field->Field_Name; ?>">
                                <?php foreach ($Options as $Option) { ?>
                                    <option value='<?php echo $Option; ?>' <?php if ($Value == $Option) {
                                        echo "Selected";
                                    } ?>><?php echo $Option; ?></option><?php } ?>
                            </select>

                        <?php
                        } elseif ($Field->Field_Type == "radio") {

                            //
                            // Display these fields in rightside div, floating to the right of the existing div
                            //

                            $Options = explode(",", $Field->Field_Options);
                            $optionPak .= "<div style='line-height:25px;display:block;padding: 0 20px;'><span style='padding-right:40px'>" . $Field->Field_Name . "</span>";

                            foreach ($Options as $Option) {
                                $optionPak .= "<div style='float:right;width:60px'><input type='radio' name='" . $Field->Field_Name . "' class='ewd-admin-small-input' rel='".$Value."' value= " . strtoupper($Option);
                                if (strtoupper($Value) == strtoupper( $Option ) ) $optionPak .= " checked";
                                if ($Value == "" && strtoupper($Option) === "NO") $optionPak .= " checked"; // no value = "NO"
                                $optionPak .= "> " . strtoupper($Option) . '</div>';
                            }

                            $optionPak .= "</div>";

                        } elseif ($Field->Field_Type == "checkbox") {
                            ?>
                            <?php $Options = explode(",", $Field->Field_Options); ?>
                            <?php $User_Checkbox = explode(",", $Value); ?>
                            <?php foreach ($Options as $Option) { ?>
                                <input type="checkbox" class='ewd-admin-small-input'
                                       name="<?php echo $Field->Field_Name; ?>[]" value="<?php echo $Option; ?>"
                                <?php if (in_array($Option, $User_Checkbox)) {
                                    echo "checked";
                                } ?>><?php echo $Option; ?></br>
                            <?php } ?>
                        <?php } ?>
                        <p><?php echo $Field->Field_Description; ?></p>
                    </div>
                <?php } ?>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button-primary"
                           value="<?php _e('Edit User ', 'EWD_FEUP') ?>"/>
                </p>

        </div>
    </div>
</div>

<div style="width:auto;margin-left:10px;border:1px solid #195b99;float:left;padding: 0 10px 20px 10px;">
    <h2><?php echo ($UserProperName->Field_Value); ?>'s Permission Settings</h2>
    <?php echo $optionPak; ?>
</div>
</form>

</div>