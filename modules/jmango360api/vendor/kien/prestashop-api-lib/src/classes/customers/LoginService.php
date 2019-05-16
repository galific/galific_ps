<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class LoginService extends BaseService
{

    public function doExecute()
    {
        if ($this->isPostMethod()) {
            $email = $this->getRequestValue('email');
            $password = $this->getRequestValue('password');
            $customer = new CustomerCore();
            $authentication = $customer->getByEmail($email, $password);

            if ($authentication && $customer->id) {
                if (!$this->isJmCustomer($customer->id)) {
                    $this->addJmCustomer($customer->id);
                }
                $this->response = new LoginResponse();
                $this->response->customer = $customer;
            } else {
                $this->response = new LoginResponse();
                $this->response->errors = array('Invalid email or password!');
            }
        } else {
            $this->throwUnsupportedMethodException();
        }
    }
}
