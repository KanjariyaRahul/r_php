<?php
include_once(__DIR__ . "/../config/connection.php");

class mdl_customerdetail {
    public $_customer_contact_detail_id;
    public $_customer_id;
    public $_person_name;
    public $_contact_no;
    public $_email_id;
    public $_is_send_sms;
    public $_is_send_email;
    public $_transactionmode;
}

class bll_customerdetail {
    public $_mdl;
    public $_dal;

    public function __construct() {
        $this->_mdl = new mdl_customerdetail();
        $this->_dal = new dal_customerdetail();
    }

    public function dbTransaction() {
        try {
            $this->_dal->dbTransaction($this->_mdl);

            // Redirect based on transaction mode
            switch ($this->_mdl->_transactionmode) {
                case "D":
                    header("Location: delete_customer.php");
                    exit;
                case "U":
                case "I":
                    header("Location: srh_customer_master.php");
                    exit;
                default:
                    header("Location: dashboard.php");
                    exit;
            }
        } catch (Exception $e) {
            error_log("Transaction Error: " . $e->getMessage());
            die("An error occurred. Please try again.");
        }
    }

    public function fillModel() {
        $this->_dal->fillModel($this->_mdl);
    }

    public function pageSearchDetail() {
        global $connect;
        $CustomerId = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : null;
        // Assuming you have a connection object $connect
        $sql = "CALL search_customer_contact_detail(:whereField)";
        $stmt = $connect->prepare($sql);

        $stmt->bindParam(':whereField', $CustomerId, PDO::PARAM_INT);
        try {
            if(!$stmt->execute()) {
                echo "error";
            }
        }
        catch(e){
            echo e;
        }
        echo "
        <table id=\"searchMaster\" class=\"table table-bordered table-striped\">
        <thead>
            <tr>
                <th>Person Name</th>
                <th>Contact No</th>
                <th>Email Id</th>
                <th>Is Send SMS</th>
                <th>Is Send Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id=\"customerTableBody\">";

        // Fetch data safely
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
            <td>{$row['person_name']}</td>
            <td>{$row['contact_no']}</td>
            <td>{$row['email_id']}</td>
            <td>" . ($row['is_send_sms'] ? 'Yes' : 'No') . "</td>
            <td>" . ($row['is_send_email'] ? 'Yes' : 'No') . "</td>
            <td>
                <form method=\"post\" action=\"frm_customer_master.php\" style=\"display:inline; margin-right:5px;\">
                    <input class=\"btn btn-default update\" type=\"submit\" name=\"btn_update\" value=\"Edit\" />
                    <input type=\"hidden\" name=\"customer_contact_detail_id\" value=\"{$row['customer_contact_detail_id']}\" />
                    <input type=\"hidden\" name=\"transactionmode\" value=\"U\" />
                </form>
                <form method=\"post\" action=\"delete_customer.php\" style=\"display:inline;\">
                    <input class=\"btn btn-default delete\" type=\"submit\" name=\"btn_delete\" value=\"Delete\" />
                    <input type=\"hidden\" name=\"customer_contact_detail_id\" value=\"{$row['customer_contact_detail_id']}\" />
                    <input type=\"hidden\" name=\"transactionmode\" value=\"D\" />
                </form>
            </td>
        </tr>";
        }
        echo "</tbody></table>";
        }
    }

class dal_customerdetail {
    public function dbTransaction($mdl) {
        global $connect;

        if ($mdl->_transactionmode == 'I' && empty($mdl->_customer_id)) {
            $mdl->_customer_id = null;
        }

       try {
        
           //$connect->beginTransaction();
       
                $stmt = $connect->prepare("CALL transaction_customer_contact_detail(?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bindParam(1, $mdl->_customer_contact_detail_id, PDO::PARAM_INT);
                $stmt->bindValue(2, $mdl->_customer_id, PDO::PARAM_INT);
                $stmt->bindParam(3, $mdl->_person_name, PDO::PARAM_STR);
                $stmt->bindParam(4, $mdl->_contact_no, PDO::PARAM_STR);
                $stmt->bindParam(5, $mdl->_email_id, PDO::PARAM_STR);
                $stmt->bindParam(6, $mdl->_is_send_sms, PDO::PARAM_STR);
                $stmt->bindParam(7, $mdl->_is_send_email, PDO::PARAM_STR);
                $stmt->bindParam(8, $mdl->_transactionmode, PDO::PARAM_STR);
                $stmt->execute();
               
            
           //$connect->commit();
       } catch (Exception $e) {
           /* $connect->rollBack();
           error_log($e->getMessage());
           echo "Error: " . $e->getMessage(); */
       }
    }

    public function fillModel($mdl) {
        global $connect;

        try {
            $customer_contact_detail_id = filter_input(INPUT_POST, 'customer_contact_detail_id', FILTER_VALIDATE_INT);
            if (!$customer_contact_detail_id) {
                throw new Exception("Invalid customer ID.");
            }

            $sql = "SELECT * FROM tbl_customer_contact_detail WHERE customer_contact_detail_id = :customer_contact_detail_id";
            $stmt = $connect->prepare($sql);
            $stmt->bindParam(':customer_contact_detail_id', $customer_contact_detail_id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $mdl->_customer_contact_detail_id = $row['customer_contact_detail_id'];
                $mdl->_customer_id = $row['customer_id'];
                $mdl->_person_name = $row['person_name'];
                $mdl->_contact_no = $row['contact_no'];
                $mdl->_email_id = $row['email_id'];
                $mdl->_is_send_sms = $row['is_send_sms'];
                $mdl->_is_send_email = $row['is_send_email'];
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            die("Error fetching customer data.");
        }
    }
}

// Main script for handling form submission
$_bll_detail = new bll_customerdetail();


if(isset($_REQUEST["btn_add"]) && ($_REQUEST["btn_add"]=="Add" || $_REQUEST["btn_add"]=="Update"))
{
    $_bll_detail->_mdl->_customer_contact_detail_id = $CustomerDetailId ?: null;
    $_bll_detail->_mdl->_person_name = trim($_POST['person_name']);
    $_bll_detail->_mdl->_contact_no= trim($_POST['contact_no']);
    $_bll_detail->_mdl->_email_id = trim($_POST['email_id']);
    $_bll_detail->_mdl->_is_send_sms = isset($_POST['is_send_sms']) ? 1 : 0;
    $_bll_detail->_mdl->_is_send_email = isset($_POST['is_send_email']) ? 1 : 0;
    $_bll_detail->_mdl->_transactionmode = $transactionmode;

    // Ensure transaction is executed only once
    $_bll_detail->dbTransaction();
}
?>
