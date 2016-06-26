Feature: Endpoint URLs

  Scenario: Adding event to EndPoint URL will not mutate EndPoint URL
    Given I init application with default endpoint
    And I have EndPoint object which points to "Class.Method"
    When I add event "MockComponent0.Name.Event" to URL from getUrl method
    Then I get URL from EndPoint equal to "Class.Method"

  Scenario: Adding event to URL object retrieved from EndPoint will sustain event
    Given I init application with default endpoint
    And  I have EndPoint object which points to "Class.Method"
    When I add event "MockComponent0.Name.Event" to URL object
    Then I get URL object equal to "Class.Method{MockComponent0.Name.Event}"