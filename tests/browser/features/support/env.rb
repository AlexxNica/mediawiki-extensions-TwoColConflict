require 'require_all'
require 'mediawiki_selenium/cucumber'
require 'mediawiki_selenium/pages'
require 'mediawiki_selenium/step_definitions'
require_all File.dirname(__FILE__) + '/../../../../../../tests/browser/features/support/pages'
require_all File.dirname(__FILE__) + '/../../../../../../tests/browser/features/step_definitions'