<?php
session_start();
$page = "customer";

// Include necessary files
include('config/connection.php');
include('classes/cls_customer_master.php');

// Redirect if the session is not set
if (!isset($_SESSION['ad_session'])) {
    header('Location: index.php');
    exit();
}

$_bll = new bll_customermaster();// Initialize Business Logic Layer
$_bll_detail = new bll_customerdetail();

// If editing, populate model
$CustomerId = isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : null;
if ($CustomerId) {
    $_REQUEST['customer_id'] = $CustomerId;
    $_bll->fillModel();
    $customer = $_bll->_mdl;
}
$CustomerDetailId = isset($_REQUEST['customer_contact_detail_id']) ? $_REQUEST['customer_contact_detail_id'] : null;
if ($CustomerDetailId) {
    $_REQUEST['customer_contact_detail_id'] = $CustomerDetailId;
    $_bll_detail->fillModel();
    $CustomerDetail = $_bll_detail->_mdl;
}

// Determine transaction mode: "U" for update, "I" for insert
$transactionmode = $CustomerId ? "U" : "I";

// Save logic (Insert or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inputSave'])) {

    $_bll->_mdl->_customer_id = $CustomerId ?: null;
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
    $_bll_detail->_mdl->_send_sms = isset($_POST['is_send_sms']) ? 1 : 0;  // Updated field name
    $_bll_detail->_mdl->_send_email = isset($_POST['is_send_email']) ? 1 : 0;  // Updated field name
    $_bll->_mdl->_status = $_POST['inputStatus'] ?? '';
    $_bll->_mdl->_transactionmode = $transactionmode;
    // Ensure transaction is executed only once
    $_bll->dbTransaction();
    $_bll_detail->_mdl->_transactionmode = $transactionmode;
    $_bll_detail->dbTransaction();
}

// Fetch mappings for dropdowns
try {
    $stmtStates = $connect->prepare("SELECT state_id, state_name FROM tbl_state_master ORDER BY state_name ASC");
    $stmtStates->execute();
    $states = $stmtStates->fetchAll(PDO::FETCH_ASSOC);

    $stmtCities = $connect->prepare("SELECT city_id, city_name, state_id, country_id FROM tbl_city_master ORDER BY city_name ASC");
    $stmtCities->execute();
    $cities = $stmtCities->fetchAll(PDO::FETCH_ASSOC);

    $stmtDistricts = $connect->prepare("SELECT district_id, district_name, city_id, state_id, country_id FROM tbl_district_master ORDER BY district_name ASC");
    $stmtDistricts->execute();
    $districts = $stmtDistricts->fetchAll(PDO::FETCH_ASSOC);

    $stmtCountries = $connect->prepare("SELECT country_id, country_name FROM tbl_country_master ORDER BY country_name ASC");
    $stmtCountries->execute();
    $countries = $stmtCountries->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading data: " . $e->getMessage();
}
?>
<?php include("include/header.php"); ?>
<?php include("include/body_open.php"); ?>

<div class="wrapper">
    <?php include("include/navigation.php"); ?>
    <div class="content-wrapper">
        <section class="content-header">
            <h1><?php echo $CustomerId ? 'Edit Customer' : 'Add Customer'; ?></h1>
        </section>

        <section class="content">
            <div class="col-md-12" style="padding:0;">
                <div class="box box-info">
                    <form class="form-horizontal" name="main_form" id="main_form" action="frm_customer_master.php" method="post">
                        <div class="box-body">
                            <input type="hidden" name="customer_id" value="<?php echo $customer->_customer_id ?? ''; ?>" />
                            <input type="hidden" name="transactionmode" value="<?php echo isset($customer) ? 'U' : 'I'; ?>" />

                            <div class="row" style="margin:auto">

                                <!-- Customer Name Column -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCustomerName" class="control-label col-sm-4">Customer Name*</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="inputCustomerName" name="inputCustomerName" 
                                                   placeholder="Enter Customer Name" value="<?php echo htmlspecialchars(isset($customer->_customer_name) ? $customer->_customer_name : ''); ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Type Column -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCustomerType" class="control-label col-sm-4">Customer Type*</label>
                                        <div>
                                            <label class="radio-inline">
                                                <input type="radio" name="inputCustomerType" value="local" <?php echo isset($customer->_customer_type) && $customer->_customer_type === 'local' ? 'checked' : ''; ?>>
                                                Local
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="inputCustomerType" value="global" <?php echo isset($customer->_customer_type) && $customer->_customer_type === 'global' ? 'checked' : ''; ?>>
                                                Global
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Address Column -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputAddress" class="control-label col-sm-4">Address*</label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" id="inputAddress" name="inputAddress" placeholder="Enter Address" required><?php echo htmlspecialchars(isset($customer->_address) ? $customer->_address : ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Grid layout for three columns -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inputDistrictName" class="col-sm-4 control-label">District*</label>
                                            <div class="col-sm-8">
                                                 <select class="form-control" id="inputDistrictName" name="inputDistrictName" required>
                                                     <option value="">Select District</option>
                                                     <?php foreach ($districts as $Districtoption): ?>
                                                     <option 
                                                             value="<?php echo htmlspecialchars($Districtoption['district_id']); ?>"
                                                             data-city-id="<?php echo htmlspecialchars($Districtoption['city_id']); ?>"
                                                             data-state-id="<?php echo htmlspecialchars($Districtoption['state_id']); ?>"
                                                             data-country-id="<?php echo htmlspecialchars($Districtoption['country_id']); ?>"
                                                             <?php echo isset($customer->_district_id) && $customer->_district_id == $Districtoption['district_id'] ? 'selected' : ''; ?>>
                                                         <?php echo htmlspecialchars($Districtoption['district_name']); ?>
                                                     </option>
                                                     <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inputCityName" class="col-sm-4 control-label">City*</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="inputCityName" name="inputCityName" 
                                                       value="<?php echo isset($district->_city_id) && isset($stateMapping[$district->_city_id]) ? htmlspecialchars($stateMapping[$district->_city_id]) : ''; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inputStateName" class="col-sm-4 control-label">State*</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="inputStateName" name="inputStateName" 
                                                       value="<?php echo isset($district->_state_id) && isset($stateMapping[$district->_state_id]) ? htmlspecialchars($stateMapping[$district->_state_id]) : ''; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inputCountryName" class="col-sm-4 control-label">Country*</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="inputCountryName" name="inputCountryName" 
                                                       value="<?php echo isset($district->_country_name) ? htmlspecialchars($district->_country_name) : ''; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="inputCityId" name="inputCityId" value="">
                                    <input type="hidden" id="inputStateId" name="inputStateId" value="">
                                    <input type="hidden" id="inputCountryId" name="inputCountryId" value="">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inputPincode" class="col-sm-4 control-label">Pincode*</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="inputPincode" name="inputPincode" 
                                                       placeholder="Enter Pincode" value="<?php echo htmlspecialchars(isset($customer->_pincode) ? $customer->_pincode : ''); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inputContactNo" class="col-sm-4 control-label">Contact No*</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="inputContactNo" name="inputContactNo" 
                                                       placeholder="Enter Contact No" value="<?php echo htmlspecialchars(isset($customer->_contact_no) ? $customer->_contact_no : ''); ?>" required>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" id="inputSendSms" name="inputSendSms" value="1"> Send SMS
                                                    </label>
                                                </div>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" id="inputSendWhatsapp" name="inputSendWhatsapp" value="1"> Send Whatsapp
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inputWebUrl" class="col-sm-4 control-label">Web URL*</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" id="inputWebUrl" name="inputWebUrl" 
                                                       placeholder="Enter Web URL" value="<?php echo htmlspecialchars(isset($customer->_weburl) ? $customer->_weburl : ''); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inputEmailId" class="col-sm-4 control-label">Email ID*</label>
                                            <div class="col-sm-8">
                                                <input type="email" class="form-control" id="inputEmailId" name="inputEmailId" 
                                                       placeholder="Enter Email ID" value="<?php echo htmlspecialchars(isset($customer->_email_id) ? $customer->_email_id : ''); ?>" required>
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" id="inputSendEmail" name="inputSendEmail" value="1"> Send Email
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="inputStatus" class="col-sm-4 control-label">Status*</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" id="inputStatus" name="inputStatus" required>
                                                    <option value="">Select Status</option>
                                                    <option value="1" <?php echo isset($customer->_status) && $customer->_status == '1' ? 'selected' : ''; ?>>Activated</option>
                                                    <option value="0" <?php echo isset($customer->_status) && $customer->_status == '0' ? 'selected' : ''; ?>>Deactivated</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>

  <!-- Button to trigger modal -->
  <button class="btn btn-info" data-bs-toggle="modal" id="btn_add" data-bs-target="#customerModal">Add Customer Details</button>

            <div class="box-body">
                <?php
                    $_bll_detail = new bll_customerdetail(); // Change from district to customer
                    $_bll_detail->pageSearchDetail();
                ?>
                </div>
                    <div class="box-footer">
                <input type="hidden" id="customer_id" name="customer_id" value= "<?php if($transactionmode=="U") echo $_bll->_mdl->_customer_id; else echo 0; ?>">
                <input type="hidden" id="hidden_customers_data" name="hidden_customers_data" value="">
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input class="btn btn-default" type="submit" id="inputSave" name="inputSave" value= "<?php if($transactionmode=="U") echo "Update"; else echo "Add";?>">
                <input class="btn btn-default" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="reset_data();" >
                <input type="button" class="btn btn-default" id="btn_cancel" name="btn_cancel" value="Cancel"  onclick="window.location='srh_customer_master.php';">
                <input type="button" class="btn btn-default" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_customer_master.php'">
              </div>
                    </form>
            </div>
            </div>
            <!-- Modal -->
  <div class="modal" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
      <div class="modal-content">
        <form id="popupForm" method="post" class="form-horizontal">
          <div class="modal-header">
            <h4 class="modal-title" id="customerModalLabel">Add Customer Contact Details</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="box-body container-fluid">
                <div class="row">
                    <div class="col-md-6">
                    <div class="form-group">
                <label for="person_name" class="col-sm-4 control-label row">Person Name</label>
                <div class="col-sm-8">
                  <input type="text" id="person_name" name="person_name" class="form-control" placeholder="Person Name" value="<?php echo htmlspecialchars(isset($CustomerDetail->_person_name) ? $CustomerDetail->_person_name : ''); ?>" required />
                </div>
              </div>
                    </div>
                    <div class="col-md-6">
                         <div class="form-group ">
                <label for="contact" class="col-sm-4 control-label">Contact</label>
                <div class="col-sm-8">
                  <input type="text" id="contact_no" name="contact_no" class="form-control" placeholder="Customer Contact"  value="<?php echo htmlspecialchars(isset($CustomerDetail->_person_name) ? $CustomerDetail->_contact_no : ''); ?>"required />
                </div>
              </div> 
                             <div class="form-group">
                <label class="col-sm-4 control-label">Send SMS</label>
                <div class="col-sm-8">
                  <input type="checkbox" id="is_send_sms" name="is_send_sms" class="form-check-input"  value="<?php echo htmlspecialchars(isset($CustomerDetail->_is_send_sms) ? $CustomerDetail->_is_send_sms : ''); ?>" />
                </div>
              </div>
                    </div>
                    
                </div>
           <div class="row">
               <div class="col-md-4">
                    <div id="emailFields">
                <div class="form-group row">
                  <label for="email" class="col-sm-4 control-label">Email Address</label>
                  <div class="col-sm-8">
                    <input type="email" id="email_id" name="email_id" class="form-control" placeholder="Email Address"  value="<?php echo htmlspecialchars(isset($CustomerDetail->_person_name) ? $CustomerDetail->_email_id : ''); ?>" />
                  </div>
                </div>
              </div>
                    <div class="form-group">
                <label class="col-sm-4 control-label">Send Email</label>
                <div class="col-sm-8">
                  <input type="checkbox" id="is_send_email" name="is_send_email" class="form-check-input"  value="<?php echo htmlspecialchars(isset($CustomerDetail->_is_send_email) ? $CustomerDetail->_is_send_email : ''); ?>"/>
                </div>
              </div> 
               </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" value="Submit">
          </div>
        </form>
      </div>
    </div>
  </div>
        </section>
    </div>
</div>
<!-- Custom JS --><script>
   
  


document.addEventListener("DOMContentLoaded", function () {
    let customers = [];  // Initialize the customers array
    let editIndex = -1;  // Used for editing a customer

    // Open modal for adding a new customer
    document.getElementById("btn_add").addEventListener("click", function () {
        resetForm();
        editIndex = -1;
        document.getElementById("customerModalLabel").textContent = "Add Customer Contact Details";
        //new bootstrap.Modal(document.getElementById("customerModal")).show();
    });

    // Enable/disable email field based on checkbox selection
    document.getElementById("is_send_email").addEventListener("change", function () {
        document.getElementById("emailFields").style.display = this.checked ? "block" : "none";
    });

    // Handle form submission for adding or editing customer
    document.getElementById("popupForm").addEventListener("submit", function (e) {
        e.preventDefault();
        
        // Collecting form data
        let customerName = document.getElementById("person_name").value.trim();
        let contactNo = document.getElementById("contact_no").value.trim();
        let sendEmail = document.getElementById("is_send_email").checked;
        let email = document.getElementById("email_id").value.trim();
        let sendSMS = document.getElementById("is_send_sms").checked;

        console.log("Form Data:", customerName, contactNo, sendEmail, email, sendSMS); // Log form data

        // Validate form data
        if (!customerName || !contactNo) {
            alert("Please fill out all required fields.");
            return;
        }

        if (sendEmail && !email) {
            alert("Please provide an email address if 'Send Email' is selected.");
            return;
        }

        // Create the customer data object
        let customerData = { customerName, contactNo, email, sendEmail, sendSMS };

        // If editIndex is -1, add a new customer; otherwise, update the existing one
        if (editIndex === -1) {
            customers.push(customerData);  // Add new customer
        } else {
            customers[editIndex] = customerData;  // Update existing customer
        }

        console.log("Updated Customers Array:", customers); // Log updated customers array

        alert(editIndex === -1 ? "Customer added successfully!" : "Customer updated successfully!");

        // Display updated customers and reset form
        displayCustomers();
        resetForm();
        document.querySelector(".btn-close").click(); // Close modal
    });

    // Function to display the customers in a table (if needed)
    function displayCustomers() {
        let tableBody = document.getElementById("customerTableBody");
        tableBody.innerHTML = "";  // Clear the existing table data

        // Loop through the customers array and display each one
        customers.forEach((customer, index) => {
            let row = `<tr>
                <td>${customer.customerName}</td>
                <td>${customer.contactNo}</td>
                <td>${customer.email || "N/A"}</td>
                <td>${customer.sendSMS ? "Yes" : "No"}</td>
                <td>${customer.sendEmail ? "Yes" : "No"}</td>
                <td>
                    <button class='btn btn-warning btn-sm' onclick='editCustomer(${index})'>Edit</button>
                    <button class='btn btn-danger btn-sm' onclick='deleteCustomer(${index})'>Delete</button>
                </td>
            </tr>`;
            tableBody.innerHTML += row;  // Append row to the table body
        });
    }

    // Edit customer
    window.editCustomer = function (index) {
        editIndex = index;
        let customer = customers[index];

        document.getElementById("person_name").value = customer.customerName;
        document.getElementById("contact_no").value = customer.contactNo;
        document.getElementById("is_send_sms").checked = customer.sendSMS;
        document.getElementById("is_send_email").checked = customer.sendEmail;
        document.getElementById("email_id").value = customer.email;
        document.getElementById("emailFields").style.display = customer.sendEmail ? "block" : "none";

        document.getElementById("customerModalLabel").textContent = "Edit Customer Contact Details";
        new bootstrap.Modal(document.getElementById("customerModal")).show();  // Open modal for editing
    };

    // Delete customer
    window.deleteCustomer = function (index) {
        if (confirm("Are you sure you want to delete this customer?")) {
            customers.splice(index, 1);  // Remove the customer from the array
            displayCustomers();  // Refresh the customer table
        }
    };

    // Reset form fields after add or edit
    function resetForm() {
        document.getElementById("popupForm").reset();  // Reset form fields
        document.getElementById("emailFields").style.display = "none";  // Hide email fields if not needed
    }

    // Display initial customers (if any)
    //displayCustomers();
    
    document.getElementById("main_form").onsubmit = function() {
    // Prepare the customer data as a JavaScript object
        //console.log(customers);
        
    var customerData = {
      customerName: document.getElementById("person_name").value,
      contactNo: document.getElementById("contact_no").value,
      email: document.getElementById("email_id").value,
      isSendSMS: document.getElementById("is_send_sms").checked,
      isSendEmail: document.getElementById("is_send_email").checked
    };

    // Convert the object into a JSON string
    var jsonString = JSON.stringify(customers);

    // Assign the JSON string to the hidden input field
    document.getElementById("hidden_customers_data").value = jsonString;
        console.log(document.getElementById("hidden_customers_data").value);
  };
});
</script>

<script>
    const cityMapping = <?= json_encode(array_column($cities, 'city_name', 'city_id')) ?>;
    const stateMapping = <?= json_encode(array_column($states, 'state_name', 'state_id')) ?>;
    const countryMapping = <?= json_encode(array_column($countries, 'country_name', 'country_id')) ?>;

document.getElementById('inputDistrictName').addEventListener('change', function () {
    const selectedDistrict = this.options[this.selectedIndex];
    const cityId = selectedDistrict.getAttribute('data-city-id');
    const stateId = selectedDistrict.getAttribute('data-state-id');
    const countryId = selectedDistrict.getAttribute('data-country-id');

    document.getElementById('inputCityName').value = cityMapping[cityId] || '';
    document.getElementById('inputStateName').value = stateMapping[stateId] || '';
    document.getElementById('inputCountryName').value = countryMapping[countryId] || '';

    document.getElementById('inputCityId').value = cityId || '';
    document.getElementById('inputStateId').value = stateId || '';
    document.getElementById('inputCountryId').value = countryId || '';
});
 
    document.getElementById('inputDistrictName').dispatchEvent(new Event('change'));
</script>
<?php include("include/footer.php"); ?>
