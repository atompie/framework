Feature: Bootstrap

  Scenario: Bootstrap starts environment
    When I bootstrap
    Then I have environment instance in Kernel
    And Global and Kernel bootstrap environment objects are equal

  Scenario: Environment sets response content-type from accept header
    Given I destroy environment
    And I set request to have "Accept" header equal to "application/json;version=1;q=0.9"
    When I get an instance of Environment
    Then response has "Content-Type" header
    And response "Content-Type" header is equal to "application/json"
    And response Content has header Content-type is equal to "application/json"

  Scenario: Environment sets response content-type from accept header depending on acceptance order
    Given I destroy environment
    And I set request to have "Accept" header equal to "application/json;version=1.0;q=0.9,text/html;level=1"
    When I bootstrap
    Then response has "Content-Type" header
    And response Content-Type header media type equals "text/html"
    And response "Content-Type" header is equal to "text/html"
    And response Content has header Content-type is equal to "text/html"

