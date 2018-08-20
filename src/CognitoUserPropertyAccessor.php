<?php
namespace BlackBits\LaravelCognitoAuth;

class CognitoUserPropertyAccessor
{
    /**
     * @var CognitoClient
     */
    protected $cognitoClient;

    /**
     * CognitoUserPropertyAccessor constructor.
     * @param CognitoClient $cognitoClient
     */
    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    /**
     * @param string $username
     * @param string $attribute
     * @return bool|null
     */
    public function getCognitoUserAttribute(string $username, string $attribute)
    {
        $cognitoUser = $this->cognitoClient->getUser($username);

        if (!$cognitoUser) {
            return false;
        }

        $userAttributes = $cognitoUser->get('UserAttributes');
        $attributeValue = null;

        foreach ($userAttributes as $userAttribute) {
            if ($userAttribute['Name'] == $attribute) {
                $attributeValue = $this->transformValue($userAttribute['Value']);
            }
        }

        return $attributeValue;
    }

    /**
     * @param string $username
     * @return string
     */
    public function getUserStatus(string $username)
    {
        $cognitoUser = $this->cognitoClient->getUser($username);

        if (!$cognitoUser) {
            return false;
        }

        return $cognitoUser->get('UserStatus');
    }

    /**
     * @param string $value
     * @return bool|mixed
     */
    private function transformValue(string $value)
    {
        switch($value) {
            case 'true':
                return true;
                break;
            case 'false':
                return false;
                break;
            default:
                return $value;
                break;
        }
    }
}