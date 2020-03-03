<?php
require '../vendor/autoload.php';
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$host = 'http://127.0.0.1:4444';
$driver = null;

try {
    $options = new ChromeOptions();
    $options->addArguments([
        '--headless',
        '--window-size=700,700',
        '--ignore-certificate-errors',
        '--disable-popup-blocking',
        '--disable-web-security',
        '--disable-javascript',
        '--start-maximized',
        '--incognito',
        '--no-sandbox',
        '--disable-infobars',
        '--disable-dev-shm-usage',
        '--disable-browser-side-navigation',
        '--disable-gpu',
        '--disable-features=VizDisplayCompositor'
    ]);

    $options->setExperimentalOption('excludeSwitches', ['enable-automation']);
    $caps = DesiredCapabilities::chrome();

    $caps->setCapability('pageLoadStrategy', 'none');
    $caps->setCapability(ChromeOptions::CAPABILITY, $options);
    $driver = RemoteWebDriver::create($host, $caps);
    $driver->get('http://google.co.jp');

    //フッター要素が出るまで待機する
    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id("foot"))
    );

    //ページのHTMLソース
    $source = $driver->getPageSource();
    var_dump($source);

    $driver->wait(5);
    $driver->close();

}catch (\Facebook\WebDriver\Exception\WebDriverException $e){
    var_dump($e);
}

try{
    //確実に終了する
    if($driver != null && $driver instanceof RemoteWebDriver){
        $driver->close();
    }
}catch (\Facebook\WebDriver\Exception\WebDriverException $e){
    var_dump($e);
}