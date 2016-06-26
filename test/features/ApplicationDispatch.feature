Feature: Application action dispatching

  Scenario: Application returns response object with endpoint data
    Given I define config as "\WorkshopTest\Resource\Config\Config"
    When I run application endpoint "WorkshopTest_Resource_EndPoint_DefaultController.yes"
    Then Application should return response
    And Response content equals "yes"
    And Response status equals "200"

  Scenario: Application fires event
    Given I define config as "\WorkshopTest\Resource\Config\Config"
    When I run application endpoint "WorkshopTest_Resource_EndPoint_DefaultController.yes" and event "WorkshopTest_Resource_Component_Yes.Test.No"
    Then Application should return response
    And Response content equals "no"
    And Response status equals "200"

  Scenario: Application fires event with short event spec and short endpoint
    Given I define config as "\WorkshopTest\Resource\Config\Config"
    When I run application endpoint "DefaultController.yes" and event "Yes.Test.No"
    Then Application should return response
    And Response content equals "no"
    And Response status equals "200"

  Scenario: Application fires event with namespaced event spec and namespaced endpoint
    Given I define config as "\WorkshopTest\Resource\Config\Config"
    When I run application endpoint "EndPoint_DefaultController.yes" and event "Component_Yes.Test.No"
    Then Application should return response
    And Response content equals "no"
    And Response status equals "200"

  Scenario: Application can call static endpoint
    Given I define config as "\WorkshopTest\Resource\Config\Config"
    When I run application endpoint "MockInterfaceEndPoint.staticJsonCall"
    Then Application should return response
    And Response content equals "true"
    And Response status equals "200"

  Scenario: Application fails with incorrect endpoint definition
    Given I define config as "\WorkshopTest\Resource\Config\Config"
    When I run application endpoint "EndPoint_DefaultController"
    Then Application should return response
    And Response status equals "404"


