Feature: Dynamic param

  Scenario: Auto loading component param to endpoint on __create method
    Given I have REQUEST set to value "Id" = ["MockComponent4"=>"1"]
    And I init endpoint "MockInterfaceEndPoint.index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" exists
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" has action "index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" returns page "\WorkshopTest\Resource\Page\MockPage" through action "index"
    And Page "\WorkshopTest\Resource\Page\MockPage" has component "\WorkshopTest\Resource\Component\MockComponent4" as property "Component"
    When I set event listener for dispatcher event "@AfterEndPointInvoke" for param "Id" interception
    And I exec application
    Then I can read param "Id" and it equals "1"

  Scenario: Auto loading named component param to endpoint on __create method
    Given I have REQUEST set to value "Id" = ["MockComponent4.Name4"=>"1"]
    And I init endpoint "MockInterfaceEndPoint.index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" exists
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" has action "index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" returns page "\WorkshopTest\Resource\Page\MockPage" through action "index"
    And Page "\WorkshopTest\Resource\Page\MockPage" has component "\WorkshopTest\Resource\Component\MockComponent4" named "Name4" as property "Component"
    When I set event listener for dispatcher event "@AfterEndPointInvoke" for param "Id" interception
    And I exec application
    Then I can read param "Id" and it equals "1"

  Scenario: Not auto loading of component param to endpoint on __create method if wrong component type
    Given I have REQUEST set to value "Id" = ["WRONG_COMPONENT"=>"value"]
    And I init endpoint "MockInterfaceEndPoint.index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" exists
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" has action "index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" returns page "\WorkshopTest\Resource\Page\MockPage" through action "index"
    And Page "\WorkshopTest\Resource\Page\MockPage" has component "\WorkshopTest\Resource\Component\MockComponent4" named "Name4" as property "Component"
    When I set event listener for dispatcher event "@AfterEndPointInvoke" for param "Id" interception
    And I exec application
    Then Param "Id" is not set

  Scenario: Not auto loading of named component param to endpoint on __create method if wrong name
    Given I have REQUEST set to value "Id" = ["MockComponent4.WRONG_NAME"=>"value"]
    And I init endpoint "MockInterfaceEndPoint.index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" exists
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" has action "index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" returns page "\WorkshopTest\Resource\Page\MockPage" through action "index"
    And Page "\WorkshopTest\Resource\Page\MockPage" has component "\WorkshopTest\Resource\Component\MockComponent4" named "Name4" as property "Component"
    When I set event listener for dispatcher event "@AfterEndPointInvoke" for param "Id" interception
    And I exec application
    Then Param "Id" is not set

  Scenario: Auto loading global param to endpoint on __create method
    Given I have REQUEST set to "GlobalId"="value"
    And I init endpoint "MockInterfaceEndPoint.index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" exists
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" has action "index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" returns page "\WorkshopTest\Resource\Page\MockPage" through action "index"
    And Page "\WorkshopTest\Resource\Page\MockPage" has component "\WorkshopTest\Resource\Component\MockComponent4" as property "Component"
    When I set event listener for dispatcher event "@AfterEndPointInvoke" for param "GlobalId" interception
    And I exec application
    Then I can read param "GlobalId" and it equals "value"

  Scenario: No auto loading global param to endpoint on __create method due to wrong param type. Param Id is local param so it should not load.
    Given I have REQUEST set to "Id"="value"
    And I init endpoint "MockInterfaceEndPoint.index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" exists
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" has action "index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" returns page "\WorkshopTest\Resource\Page\MockPage" through action "index"
    And Page "\WorkshopTest\Resource\Page\MockPage" has component "\WorkshopTest\Resource\Component\MockComponent4" as property "Component"
    When I set event listener for dispatcher event "@AfterEndPointInvoke" for param "Id" interception
    And I exec application
    Then Param "Id" is not set

  Scenario: Auto loading global param constrained to int value to endpoint on __create method
    Given I have REQUEST set to "IntValue"="1"
    And I init endpoint "MockInterfaceEndPoint.index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" exists
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" has action "index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" returns page "\WorkshopTest\Resource\Page\MockPage" through action "index"
    And Page "\WorkshopTest\Resource\Page\MockPage" has component "\WorkshopTest\Resource\Component\MockComponent4" as property "Component"
    When I set event listener for dispatcher event "@AfterEndPointInvoke" for param "IntValue" interception
    And I exec application
    Then I can read param "IntValue" and it equals "1"

  Scenario: Throwing exception on auto loading global param constrained to int value to endpoint on __create method
    Given I have REQUEST set to "IntValue"="NOT_INT"
    And I init endpoint "MockInterfaceEndPoint.index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" exists
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" has action "index"
    When I set event listener for dispatcher event "@AfterEndPointInvoke" for param "IntValue" interception
    Then When I run application I get exception with message "Parameter [IntValue] has not passed its constrain rules.".

  Scenario: Throwing exception on auto loading component param constrained to int value to endpoint on __create method
    Given I have REQUEST set to "Id"="NOT_INT"
    And I init endpoint "MockInterfaceEndPoint.index"
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" exists
    And Endpoint "\WorkshopTest\Resource\EndPoint\MockInterfaceEndPoint" has action "index"
    When I set event listener for dispatcher event "@AfterEndPointInvoke" for param "Id" interception
    Then When I run application I get exception with message "Parameter [Id] has not passed its constrain rules.".