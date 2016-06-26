Feature: Dynamic use case invoking

  # Invoking

#  Scenario: Invoking dynamic use case method with [repository] as parameter
#    Given I start application
#    And I have [UseCase] class with method "getData" and it has parameter of type Repository
#    When I invoke [UseCase] method "getData" via UseCaseMethodInvoker
#    Then I get [UseCase] property filled with repository object
#
#  Scenario: Invoking use case method with [session] as parameter
#    Given I have global SESSION variable set to "MySession1"="session-data"
#    And I start application
#    And I have [UseCase] class with method "getSession" and it has parameter of type Session
#    When I invoke [UseCase] method "getSession" via UseCaseMethodInvoker
#    Then I get [UseCase] property filled with session value equal to "session-data"
#
#  Scenario: Invoking use case method with EMPTY [session] as parameter
#    Given I have empty SESSION
#    And I start application
#    And I have [UseCase] class with method "getSession" and it has parameter of type Session
#    When I invoke [UseCase] method "getSession" via UseCaseMethodInvoker
#    Then I get [UseCase] property filled with session value equal to NULL
#
#  Scenario: Invoking use case method with [SessionJar] as parameter
#    Given I have global SESSION variable set to "MySession2"="session-data"
#    And I start application
#    And I have [UseCase] class with method "getSessionJar" and it has parameter of type SessionJar
#    When I invoke [UseCase] method "getSessionJar" via UseCaseMethodInvoker
#    Then I get [UseCase] property with SessionJar and session value is equal to "MySession2"="session-data"
#
#  Scenario: Invoking use case method with [CookieJar] as parameter
#    Given I have global COOKIE variable set to "MyCookie1"="cookie-data"
#    And I start application
#    And I have [UseCase] class with method "getCookieJar" and it has parameter of type CookieJar
#    When I invoke [UseCase] method "getCookieJar" via UseCaseMethodInvoker
#    Then I get [UseCase] property filled with Cookie value equal to "MyCookie1"="cookie-data"

  Scenario: Invoking use case method with [Annotated Param] as parameter
    Given I have global REQUEST variable set to "AnnotatedParam"="value1"
    And I init application with default endpoint
    And I have [UseCase] class with method "getAnnotatedParam" and it has parameter of type MyParam1
    When I invoke [UseCase] method "getAnnotatedParam" via UseCaseMethodInvoker
    Then I get [UseCase] property filled with Param value equal to "AnnotatedParam"="value1"

    # Mocking

  Scenario: UseCases should be able to mock
    Given I have [UseCase] class with method "getData" and it has parameter of type Repository
    When I run method "getDataFromRepo" on [UseCase] with mocked repository with data "mocked-data"
    Then I get mocked value of "mocked-data"
