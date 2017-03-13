<?php
if (!class_exists('FEUP_User')){
    class FEUP_User {
    	private $Username;
		private $User_ID;

		function __construct($params = array()) {
			global $wpdb, $ewd_feup_user_table_name;

			if (isset($params['ID'])) {
				$this->Username = $wpdb->get_var($wpdb->prepare("SELECT Username FROM $ewd_feup_user_table_name WHERE User_ID=%d", $params['ID']));
				$this->User_ID = $wpdb->get_var($wpdb->prepare("SELECT User_ID FROM $ewd_feup_user_table_name WHERE User_ID=%d", $params['ID']));
			}
			elseif (isset($params['Username'])) {
				$this->Username = $wpdb->get_var($wpdb->prepare("SELECT Username FROM $ewd_feup_user_table_name WHERE Username=%s", $params['Username']));
				$this->User_ID = $wpdb->get_var($wpdb->prepare("SELECT User_ID FROM $ewd_feup_user_table_name WHERE Username=%s", $params['Username']));
			}
			else {
				$CheckCookie = CheckLoginCookie();
				if ($CheckCookie['Username'] != "") {
					$this->Username = $CheckCookie['Username'];
					$this->User_ID = $wpdb->get_var($wpdb->prepare("SELECT User_ID FROM $ewd_feup_user_table_name WHERE Username=%s", $this->Username));
				}
			}
    	}

		function Get_User_Name_For_ID($id = null) {
			global $wpdb, $ewd_feup_user_table_name;

			if(!$id) {
				return null;
			}

			return $wpdb->get_var($wpdb->prepare("SELECT Username FROM $ewd_feup_user_table_name WHERE User_ID=%d", $id));
		}

		function Get_Field_Value_For_ID($Field, $id) {
			global $wpdb, $ewd_feup_user_fields_table_name;

			if(!$Field || !$id) {
				return null;
			}
			$Value = $wpdb->get_var($wpdb->prepare("SELECT Field_Value FROM $ewd_feup_user_fields_table_name WHERE Field_Name=%s AND User_ID=%d", $Field, $id));

			return $Value;
		}

		function Get_User_ID() {
			return $this->User_ID;
		}

		function Get_Username() {
			return $this->Username;
		}

		function Get_User_Level_Name() {
			global $wpdb, $ewd_feup_user_table_name, $ewd_feup_levels_table_name;

			$Level_ID = $wpdb->get_var($wpdb->prepare("SELECT Level_ID FROM $ewd_feup_user_table_name WHERE User_ID=%d", $this->User_ID));
			$Level_Name = $wpdb->get_var($wpdb->prepare("SELECT Level_Name FROM $ewd_feup_levels_table_name WHERE Level_ID=%d", $Level_ID));

			return $Level_Name;
		}

        function Get_User_Level_ID() {
            global $wpdb, $ewd_feup_user_table_name, $ewd_feup_levels_table_name;

            $Level_ID = $wpdb->get_var($wpdb->prepare("SELECT Level_ID FROM $ewd_feup_user_table_name WHERE User_ID=%d", $this->User_ID));
            $Level_Name = $wpdb->get_var($wpdb->prepare("SELECT Level_Name FROM $ewd_feup_levels_table_name WHERE Level_ID=%d", $Level_ID));

            return $Level_ID;
        }

		function Get_Field_Value($Field) {
			global $wpdb, $ewd_feup_user_fields_table_name;

			$Value = $wpdb->get_var($wpdb->prepare("SELECT Field_Value FROM $ewd_feup_user_fields_table_name WHERE Field_Name=%s AND User_ID=%d", $Field, $this->User_ID));

			return $Value;
		}

    	function Is_Logged_In() {
			$CheckCookie = CheckLoginCookie();
			if ($this->Username == $CheckCookie['Username'] and isset($this->Username)) {return true;}
			else {return false;}
    	}


        /**
         * @return mixed
         * @description User's data by UserID
         * @author hawkwynd
         */
        function get_users_data() {
            global $wpdb;
            $UserIDs = $wpdb->get_results("SELECT User_ID FROM wp_EWD_FEUP_Users");
            foreach($UserIDs as $ID){
                $birthdays[$ID->User_ID] = $wpdb->get_results(
                    "SELECT UF.*
                    FROM wp_EWD_FEUP_Users U
                    JOIN wp_EWD_FEUP_User_Fields UF on UF.User_ID=U.User_ID
                    WHERE U.User_ID = ". $ID->User_ID ."
                    ORDER BY U.User_ID"
                );
            }

            return $birthdays;
        }

        /**
         * @param null $ID
         * @return string
         * @description Get Proper Name of user by User_ID
         * @author hawkwynd
         */
        function get_user_properName($ID = null){
            if(!$ID){
                return 'NO ID';
            }
            global $wpdb;
            $username = $wpdb->get_results(
                "SELECT Field_Value as Name
                 FROM wp_EWD_FEUP_User_Fields
                 WHERE Field_Name = 'Name' AND User_ID = ". $ID);
            return $username[0]->Name;
        }

        /**
         * @return array
         * array(UserName, Date)
         * @author hawkwynd
         */
        function current_birthdays(){
            global $wpdb;
            $results = array();

            $birthdays = $wpdb->get_results(
                "SELECT  DAY(Field_Value) BDAY, User_ID
                 FROM wp_EWD_FEUP_User_Fields
                WHERE Field_Name = 'Birthday Date' and MONTH(Field_Value) = MONTH(now())"
            );

            foreach($birthdays as $rec){
                if($rec->User_ID > 0){
                    $results[$this->get_user_properName($rec->User_ID)] = $rec->BDAY;
                }
            }
            asort($results);
            return $results;
        }

        /**
         * @return array
         * array(UserName, Date)
         * @author hawkwynd
         */
        function current_anniversaries(){
            global $wpdb;
            $results = array();
            $anniversaries = $wpdb->get_results(
              "SELECT Field_Value Aday, User_ID
               FROM wp_EWD_FEUP_User_Fields
               WHERE Field_Name = 'Anniversary Date' and MONTH(Field_Value) = MONTH(now())"
            );

            foreach($anniversaries as $rec){
                if($rec->User_ID > 0) {
                    $results[$this->get_user_properName($rec->User_ID)] = $rec->Aday;
                }
            }
            asort($results);
            return $results;
        }

        /**
         * Call all users, and associative data for each user, array([Office] => [Name, Designation,Phone, Email]
         * @author hawkwynd
         *
         * ignore these users as they're developers test accounts:
         * acostag      - 15300
         * hawkwynd     - 15105
         * mindreactor  - 15228
         * test         - 15268
         */

        function EWD_FEUP_Get_All_Users( $ewd_feup_user_table_name = 'wp_EWD_FEUP_User_Fields' ) {
            global $wpdb, $ewd_feup_user_table_name;
            $User_Array = array();

            $WP_User_Objects = $wpdb->get_results("SELECT DISTINCT User_ID, Field_Value Office
                                                   FROM wp_EWD_FEUP_User_Fields
                                                   WHERE User_ID NOT IN(0, 15105, 15300, 15228,15268)
                                                   AND Field_Name IN ('Office')
                                                   AND Field_Value !=''
                                                   ");

            foreach ($WP_User_Objects as $User_Object) {
             $UserData = $wpdb->get_results("SELECT Field_Name, Field_Value
                                             FROM wp_EWD_FEUP_User_Fields
                                             WHERE User_ID ='". $User_Object->User_ID ."'
                                             AND Field_Name IN ('Name','Designation','Phone','Email')
                                             ");
                 foreach($UserData as $row){
                     $User_Array[$User_Object->Office][$User_Object->User_ID][$row->Field_Name] = $row->Field_Value;
                 }
            }

            ksort($User_Array, SORT_DESC); // Sort Offices A-z

            return $User_Array;
        }
    }
}




?>
