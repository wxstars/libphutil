<?php

/**
 * Authentication adapter for Ucai OAuth2.
 */
final class PhutilUcaiAuthAdapter extends PhutilOAuthAuthAdapter {

  public function getAdapterType() {
    return 'ucai';
  }

  public function getAdapterDomain() {
    return 'ucai.cn';
  }

  public function getAccountID() {
    return $this->getOAuthAccountData('uid');
  }

  public function getAccountEmail() {
    return $this->getOAuthAccountData('email');
  }

  public function getAccountName() {
    return $this->getOAuthAccountData('uname');
  }

  public function getAccountImageURI() {
    return $this->getOAuthAccountData('logo');
  }

  public function getAccountURI() {
    $name = $this->getAccountName();
    if (strlen($name)) {
      return 'https://ucai.com/'.$name;
    }
    return null;
  }

  public function getAccountRealName() {
    return $this->getOAuthAccountData('name');
  }

  protected function getAuthenticateBaseURI() {
    return 'http://www.ucai.cn/index.php?app=sapi&mod=Oauth&act=authorize';
  }

  protected function getTokenBaseURI() {
    return 'http://www.ucai.cn/index.php?app=sapi&mod=Oauth&act=getAccessToken';
  }

  protected function loadOAuthAccountData() {
    $uri = new PhutilURI('http://www.ucai.cn/index.php?app=sapi&mod=Oauth&act=getLoggedInUser');
    $uri->setQueryParam('access_token', $this->getAccessToken());

    $future = new HTTPSFuture($uri);

    // NOTE: Ucai requires a User-Agent string.
    $future->addHeader('User-Agent', __CLASS__);

    list($body) = $future->resolvex();

    try{
      return phutil_json_decode($body);
    } catch (PhutilJSONParserException $ex) {
      throw new PhutilProxyException(
        pht('Expected valid JSON response from Ucai account data request.'),
        $ex);
    }
  }

}
