<?php
require '../vendor/autoload.php';
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$host = 'http://127.0.0.1:4444';

$driver = null;

try {
    $options = new ChromeOptions();
    $options->addArguments([
        '--headless',
        '--window-size=1920,1080',
        '--ignore-certificate-errors',
        '--disable-popup-blocking',
        '--disable-web-security',
        '--start-maximized',
        '--incognito',
        '--no-sandbox',
        '--disable-infobars',
        '--disable-dev-shm-usage',
        '--disable-browser-side-navigation',
        '--disable-gpu',
        '--disable-features=VizDisplayCompositor',
    ]);


    $options->setExperimentalOption('excludeSwitches', ['enable-automation']);
    $caps = DesiredCapabilities::chrome();

    $caps->setCapability('pageLoadStrategy', 'none');
    $caps->setCapability(ChromeOptions::CAPABILITY, $options);
    $driver = RemoteWebDriver::create($host, $caps);

    $driver->get('http://ipleak.net');
    $driver->wait(30, 500)->until(
       WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector(".dns_box"))
    );
    //ページのHTMLソース
    $source = $driver->getPageSource();
    var_dump($source);
    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector(".dns_box .ipv4_box"))
    );
    $driver->takeScreenshot('myip1.png');

}catch (\Facebook\WebDriver\Exception\WebDriverException $e){
    //var_dump($e);
    var_dump($e->getMessage());
}

try{
    //確実に終了する
    if($driver != null && $driver instanceof RemoteWebDriver){
        $driver->close();
    }
}catch (\Facebook\WebDriver\Exception\WebDriverException $e){
    var_dump($e->getMessage());
}