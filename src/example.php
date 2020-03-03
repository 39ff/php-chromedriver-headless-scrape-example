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
        '--window-size=1920,1080',
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

    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector("form input[type=text]"))
    );

    $driver
        ->findElement(WebDriverBy::name('q'))
        ->sendKeys('ブラックハットSEO　方法')
        ->submit();

    //フッター要素が出るまで待機する
    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id("foot"))
    );
    $searchResults = $driver->findElements(WebDriverBy::cssSelector("#search .srg .g"));
    foreach ($searchResults as $remoteWebElement){
        $href = $remoteWebElement->findElement(WebDriverBy::cssSelector('.r a'))->getAttribute("href");
        var_dump($href);
    }
    //ページのHTMLソース
    $source = $driver->getPageSource();
    file_put_contents("google_search_result.html",$source);
    $driver->takeScreenshot('google_search.png');

    //最初の検索結果1件をクリック
    $driver->findElement(WebDriverBy::cssSelector("#search .srg .g a"))->click();
    $driver->wait(10, 500)->until(
        WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::tagName("body"))
    );
    $driver->takeScreenshot('google_search_first_page.png');

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