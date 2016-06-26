Feature: Kernel

  Scenario: Kernel init
    When I run default kernel init
    Then I get default dispatch object with default endpoint "Default.index"

  Scenario: Kernel has request, response, server, session, env, etc.
    Given I have set _REQUEST, _SERVER, _ENV
    And Request end point query string is "Endpoint.action"
    When I run kernel init within context of request, server, and env
    Then I get access to object of request
    And Request has endpoint value "Endpoint.action"
    And I get access to response
    And I get access to server

  Scenario: Environment with session loads with Kernel Init
    Given I start session
    Given I have global SESSION variable set to "session"="1"
    When I run default kernel init
    Then I can read session from environment
    And session variable from environment equals "session"="1"

  Scenario: Session can be set from global environment
    Given I start session
    When I run default kernel init
    Then I can set session via environment
    And it lands in global SESSION variable

