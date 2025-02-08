<?php
include_once(__DIR__ . "/../config/connection.php");
include("classes/cls_customer_contact_detail.php");

class mdl_customermaster {
    public $_customer_id;
    public $_customer_name;
    public $_customer_type;
    public $_address;
    public $_district_id;
    public $_city_id;
    public $_state_id;
    public $_country_id;
    public $_pincode;
    public $_contact_no;
    public $_send_sms;
    public $_send_whatsapp;
    public $_weburl;
    public $_email_id;
    public $_send_email;
    public $_status;
    public $_created_date;
    public $_created_by;
    public $_modified_date;
    public $_modified_by;
    public $_transactionmode;
    public $_array_detail;
}

class bll_customermaster {
    public $_mdl;
    public $_dal;

    public function __construct() {
        $this->_mdl = new mdl_customermaster();
        $this->_dal = new dal_customermaster();
    }

    public function dbTransaction() {
        $currentDate = time();
        $userId = 1; // Placeholder for user ID, replace as necessary

        // Set created or modified date and user ID based on transaction mode
        if ($this->_mdl->_transactionmode == 'I') {
            $this->_mdl->_created_date = $currentDate;
            $this->_mdl->_created_by = $userId;
        }

        if (in_array($this->_mdl->_transactionmode, ['I', 'U'])) {
            $this->_mdl->_modified_date = $currentDate;
            $this->_mdl->_modified_by = $userId;
        }
       
        
// Save logic (Insert or Update)
/*if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inputSave'])) {
    // Decode the customers JSON data received in the hidden input field
$customers = isset($_POST['hidden_customers_data']) ? json_decode($_POST['hidden_customers_data'], true) : [];
 
    if ($customers && is_array($customers)) {
        foreach ($customers as $customer) {
            // Process each customer
            $_bll_detail->_mdl->_customer_contact_detail_id = $_customer_contact_detail_id ?: null;
            $_bll_detail->_mdl->_customer_id = $_POST['customer_id'] ?? '';
            $_bll_detail->_mdl->_person_name = trim($_POST['person_name']);  // Your field
            $_bll_detail->_mdl->_contact_no = $_POST['contact_no'] ?? '';  // Your field
            $_bll_detail->_mdl->_email_id = trim($_POST['email_id']) ?? '';  // Your field
            $_bll_detail->_mdl->_send_sms = isset($_POST['is_send_sms']) ? 1 : 0;  // Your field
            $_bll_detail->_mdl->_send_email = isset($_POST['is_send_email']) ? 1 : 0;  // Your field
            $_bll_detail->_mdl->_transactionmode = $transactionmode;

            // Perform the database transaction (insert or update)
            $_bll_detail->dbTransaction();  // Assuming this is your DB handling function
        }

        // Optionally return a success message or redirect
        echo "Customer(s) saved successfully!";
    } else {
        echo "Invalid customer data.";
    }
}*/

        // Perform the transaction
        $this->_dal->dbTransaction($this->_mdl);
        
        
        /* $_bll_detail=new bll_customerdetail();
        $_bll_detail->_mdl->_customer_contact_detail_id='132';
        $_bll_detail->_mdl->_customer_id='11';
        $_bll_detail->_mdl->_person_name='ssss';
        $_bll_detail->_mdl->_email_id='asas@sdsd.com';
        $_bll_detail->_mdl->_contact_no='5555555';
        $_bll_detail->_mdl->_is_send_email='1';
        $_bll_detail->_mdl->_is_send_sms='1';
        $_bll_detail->_mdl->_transactionmode="I"; */
        
        //$_bll_detail->dbTransaction();
       
        $detail_array=$this->_mdl->_array_detail;
        
        
       
         if(!empty($detail_array)) {
            
             foreach($detail_array as $detail) {
				 
				 
				    $_bll_detail=new bll_customerdetail();
					$_bll_detail->_mdl->_customer_contact_detail_id=0;
					$_bll_detail->_mdl->_customer_id=$this->_mdl->_customer_id;
					$_bll_detail->_mdl->_person_name=$detail['customerName'];
					$_bll_detail->_mdl->_email_id=$detail['email'];
					$_bll_detail->_mdl->_contact_no=$detail['contactNo'];
					$_bll_detail->_mdl->_is_send_email=$detail['sendEmail'];
					$_bll_detail->_mdl->_is_send_sms=$detail['sendSMS'];
					$_bll_detail->_mdl->_transactionmode="I";
					
					$_bll_detail->dbTransaction();
					
           /*   echo $detail['customerName'];exit;
                 foreach($detail as $key=>$value) {
                    
                     $_bll_detail->_mdl->_person_name=$value;
					 
					 //$_bll_detail->_mdl->_customer_contact_detail_id=0;
                 $_bll_detail->_mdl->_customer_id=$this->_mdl->_customer_id;
                $_bll_detail->_mdl->_transactionmode="I";
                 $_bll_detail->dbTransaction();
				 
				 
				   
				 
                    
                 } */
                 
             }
             
         }
        
         

        // Redirect based on the transaction mode
        switch ($this->_mdl->_transactionmode) {
            case "D":
                header("Location: delete_customer.php");
                break;
            case "U":
                header("Location: srh_customer_master.php");
                break;
            case "I":
                header("Location: srh_customer_master.php");
                break;
            default:
                header("Location: dashboard.php");
                break;
        }
    }

    public function fillModel() {
        $this->_dal->fillModel($this->_mdl);
    }

    public function pageSearch() {
        global $connect;

        // SQL query to fetch customer data
        $sql = "SELECT customer_id, customer_name, country_id, contact_no, email_id, status, created_date, modified_date 
                FROM tbl_customer_master";

        echo "
        <table id=\"searchMaster\" class=\"table table-bordered table-striped\">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Country</th>
                <th>Contact No</th>
                <th>Email Id</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>Modified Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>";

        foreach ($connect->query($sql) as $row) {
            $country_name = $this->getCountryName($row['country_id']);

            // Display the rows in the table
            echo "<tr>
                <td>{$row['customer_name']}</td>
                <td>{$country_name}</td>
                <td>{$row['contact_no']}</td>
                <td>{$row['email_id']}</td>
                <td>{$row['status']}</td>
                <td>{$row['created_date']}</td>
                <td>{$row['modified_date']}</td>
                <td>
                    <form method=\"post\" action=\"frm_customer_master.php\" style=\"display:inline; margin-right:5px;\">
                        <input class=\"btn btn-default update\" type=\"submit\" name=\"btn_update\" value=\"Edit\" />
                        <input type=\"hidden\" name=\"customer_id\" value=\"{$row['customer_id']}\" />
                        <input type=\"hidden\" name=\"transactionmode\" value=\"U\" />
                    </form>
                    <form method=\"post\" action=\"delete_customer.php\" style=\"display:inline;\">
                        <input class=\"btn btn-default delete\" type=\"submit\" name=\"btn_delete\" value=\"Delete\" />
                        <input type=\"hidden\" name=\"customer_id\" value=\"{$row['customer_id']}\" />
                        <input type=\"hidden\" name=\"transactionmode\" value=\"D\" />
                    </form>
                </td>
            </tr>";
        }

        echo "</tbody></table>";
    }

    private function getCountryName($country_id) {
        global $connect;
        $sql = "SELECT country_name FROM tbl_country_master WHERE country_id = :country_id";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':country_id', $country_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['country_name'] ?? 'N/A';
    }
}

class dal_customermaster {
    public function dbTransaction($mdl) {
        global $connect;

        // Ensure transaction mode is insert (I) or update (U)
        

        try {
            $connect->exec("set @p0 = ".$mdl->_customer_id);
            // SQL query with placeholder values
            $sql = "CALL transaction_customer_master(@p0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connect->prepare($sql);
            
            // Bind values to the prepared statement
            
            $stmt->bindParam(1, $mdl->_customer_name, PDO::PARAM_STR);
            $stmt->bindParam(2, $mdl->_customer_type, PDO::PARAM_STR);
            $stmt->bindParam(3, $mdl->_address, PDO::PARAM_STR);
            $stmt->bindParam(4, $mdl->_district_id, PDO::PARAM_INT);
            $stmt->bindParam(5, $mdl->_city_id, PDO::PARAM_INT);
            $stmt->bindParam(6, $mdl->_state_id, PDO::PARAM_INT);
            $stmt->bindParam(7, $mdl->_country_id, PDO::PARAM_INT);
            $stmt->bindParam(8, $mdl->_pincode, PDO::PARAM_INT);
            $stmt->bindParam(9, $mdl->_contact_no, PDO::PARAM_STR);
            $stmt->bindParam(10, $mdl->_send_sms, PDO::PARAM_STR);
            $stmt->bindParam(11, $mdl->_send_whatsapp, PDO::PARAM_STR);
            $stmt->bindParam(12, $mdl->_weburl, PDO::PARAM_STR);
            $stmt->bindParam(13, $mdl->_email_id, PDO::PARAM_STR);
            $stmt->bindParam(14, $mdl->_send_email, PDO::PARAM_STR);
            $stmt->bindParam(15, $mdl->_status, PDO::PARAM_STR);
            $stmt->bindParam(16, $mdl->_created_date, PDO::PARAM_INT);
            $stmt->bindParam(17, $mdl->_created_by, PDO::PARAM_INT);
            $stmt->bindParam(18, $mdl->_modified_date, PDO::PARAM_INT);
            $stmt->bindParam(19, $mdl->_modified_by, PDO::PARAM_INT);
            $stmt->bindParam(20, $mdl->_transactionmode, PDO::PARAM_STR);

            // Execute the query
            if (!$stmt->execute()) {
                
                // If execution fails, log the error
                throw new Exception("Error: " . implode(" ", $stmt->errorInfo()));
            }
            
            if($mdl->_transactionmode=="I") {
                // Retrieve the output parameter
                $result = $connect->query("SELECT @p0 AS inserted_id");
                // Get the inserted ID
                $insertedId = $result->fetchColumn();
                $mdl->_customer_id=$insertedId;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function fillModel($mdl) {
        global $connect;

        try {
            // SQL to fetch customer details by ID
            $sql = "SELECT * FROM tbl_customer_master WHERE customer_id = :customer_id";
            $stmt = $connect->prepare($sql);
            $stmt->bindParam(':customer_id', $_REQUEST['customer_id'], PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $mdl->_customer_id = $row['customer_id'];
                $mdl->_customer_name = $row['customer_name'];
                $mdl->_customer_type = $row['customer_type'];
                $mdl->_address = $row['address'];
                $mdl->_district_id = $row['district_id'];
                $mdl->_city_id = $row['city_id'];
                $mdl->_state_id = $row['state_id'];
                $mdl->_country_id = $row['country_id'];
                $mdl->_pincode = $row['pincode'];
                $mdl->_contact_no = $row['contact_no'];
                $mdl->_send_sms = $row['send_sms'];
                $mdl->_send_whatsapp = $row['send_whatsapp'];
                $mdl->_weburl = $row['weburl'];
                $mdl->_email_id = $row['email_id'];
                $mdl->_send_email = $row['send_email'];
                $mdl->_status = $row['status'];
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

// Main script for handling form submission
$_bll = new bll_customermaster();
// Save logic (Insert or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inputSave'])) {

    $_bll->_mdl->_customer_id = $_REQUEST['customer_id'] ?: 0;
    $_bll->_mdl->_customer_name = trim($_POST['inputCustomerName']);
    $_bll->_mdl->_customer_type = $_POST['inputCustomerType'] ?? '';
    $_bll->_mdl->_address = trim($_POST['inputAddress']);
    $_bll->_mdl->_district_id = $_POST['inputDistrictName'] ?? '';
    $_bll->_mdl->_city_id = $_POST['inputCityId'] ?? '';
    $_bll->_mdl->_state_id = $_POST['inputStateId'] ?? '';
    $_bll->_mdl->_country_id = $_POST['inputCountryId'] ?? '';
    $_bll->_mdl->_pincode = $_POST['inputPincode'] ?? '';
    $_bll->_mdl->_contact_no = $_POST['inputContactNo'] ?? '';
    $_bll->_mdl->_send_sms = isset($_POST['inputSendSms']) ? 1 : 0;
    $_bll->_mdl->_send_whatsapp = isset($_POST['inputSendWhatsapp']) ? 1 : 0;
    $_bll->_mdl->_weburl = trim($_POST['inputWebUrl']);
    $_bll->_mdl->_email_id = trim($_POST['inputEmailId']);
    $_bll->_mdl->_send_email = isset($_POST['inputSendEmail']) ? 1 : 0;
    $_bll->_mdl->_status = $_POST['inputStatus'] ?? '';
    $_bll->_mdl->_transactionmode = $_REQUEST["transactionmode"];
    //print_r($_REQUEST['hidden_customers_data']);
    if (isset($_POST['hidden_customers_data'])) {
        //print_r($_POST['hidden_customers_data']);
       // exit;
    // Decode the JSON string into a PHP array or object
    $customerData = json_decode($_POST['hidden_customers_data'], true);
    
    if(is_array($customerData) && !empty($customerData))
    {
        
       // $_array_detail=new ArrayObject($customerData);
        $_bll->_mdl->_array_detail = $customerData;
        
    }
$_bll->dbTransaction();
    // Now you can access the individual customer data fields
    

    // Process the data as needed (e.g., save to database)
}
    //$_bll_detail->_mdl->_transactionmode = $transactionmode;
//    if(isset($_REQUEST["hidden_customers_data"])) {
//        $detail_records=json_decode($_REQUEST["hidden_customers_data"],true);
//        
//        if(!empty($detail_records)) {
//            print_r($detail_records);
//            $arrayobject = new ArrayObject($detail_records);
//              $_bll_detail->_mdl->_array_itemdetail=$arrayobject;
//        }
//    }
//   
//    // Ensure transaction is executed only once
//  
//    $_bll_detail->dbTransaction();
}


// Handle delete action
if (isset($_POST['transactionmode']) && $_POST['transactionmode'] == 'D') {
    $_bll->_mdl->_customer_id = $_POST['customer_id'];
    $_bll->_mdl->_transactionmode = 'D';
    $_bll->dbTransaction();
}
?>
