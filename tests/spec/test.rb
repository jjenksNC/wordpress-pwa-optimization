#!/usr/bin/env ruby

###
# This file hands over integration tests for rspec.
# It needs wp-cli for integrating with wordpress
###

require 'capybara/poltergeist'
require 'rspec'
require 'rspec/retry'
require 'capybara/rspec'
require 'capybara-screenshot/rspec'

 #
require 'uri' # parse the url from wp-cli

# Load our default RSPEC MATCHERS
require_relative 'lib/matchers.rb'

Capybara.save_path = "/tmp"

RSpec.configure do |config|
  config.include Capybara::DSL
  config.verbose_retry = true
  config.default_retry_count = 1
end

Capybara.configure do |config|
  config.javascript_driver = :poltergeist
  config.default_driver = :poltergeist # Tests can be more faster with rack::test.
end
 
Capybara.register_driver :poltergeist do |app|
  Capybara::Poltergeist::Driver.new(app, 
    debug: false,
    js_errors: false, # Use true if you are really careful about your site
    phantomjs_logger: '/dev/null', 
    timeout: 60,
    :phantomjs_options => [
       '--webdriver-logfile=/dev/null',
       '--load-images=no',
       '--debug=no', 
       '--ignore-ssl-errors=yes', 
       '--ssl-protocol=TLSv1'
    ],
    window_size: [1920,1080] 
   )
end


target_url = ENV['WP_TEST_URL']

uri = URI(target_url)

username = ENV['WP_TEST_USER']
password = ENV['WP_TEST_USER_PASS']

puts "testing #{target_url}..."
### Begin tests ###
describe "wordpress: #{uri}/ - ", :type => :request, :js => true do 

  subject { page }

  describe "frontpage" do

    before do
      visit "#{uri}/"
    end

    it "Healthy status code 200" do
      expect(page).to have_status_of [200]
    end

    it "Page includes stylesheets" do
      expect(page).to have_css
    end
    
  end

  describe "admin-panel" do

    before do
      visit "#{uri}/wp-login.php"
    end

    it "There's a login form" do
      expect(page).to have_id "wp-submit"
    end

    it "Logged in to WordPress Dashboard" do
      within("#loginform") do
        fill_in 'log', :with => username
        fill_in 'pwd', :with => password
      end
      click_button 'wp-submit'

      expect(page).to have_id "wpadminbar"
    end
  end

  describe "css-optimization-settings" do

    before do
      visit "#{uri}/wp-admin/themes.php?page=o10n-css&tab=optimization"
    end

    it "Logged in to WordPress Dashboard" do
      within("#loginform") do
        fill_in 'log', :with => username
        fill_in 'pwd', :with => password
      end
      click_button 'wp-submit'
      
      expect(page).to have_selector("input[name='o10n[css.minify.enabled]']")
    
      within("#poststuff") do 

        # enable cloudfront page cache
        find("input[name='o10n[css.minify.enabled]']").set(true)

        find("input[name='o10n[css.minify.concat.enabled]']").set(true)
        find("input[name='o10n[css.minify.concat.minify]']").set(true)

        find("input[name='o10n[css.minify.concat.mediaqueries.enabled]']").set(true)
        find("input[name='o10n[css.minify.concat.inline.enabled]']").set(true)

        find(".advanced-toggle-all").click;

      end
      
      click_button 'is_submit', match: :first

      expect(page).to have_content("Settings saved.")

    end

  end

  # install plugin from plugin index
  describe "plugin-index-reinstall" do

    before do
      visit "#{uri}/wp-admin/plugins.php"
    end

    it "Logged in to WordPress Dashboard" do
      within("#loginform") do
        fill_in 'log', :with => username
        fill_in 'pwd', :with => password
      end
      click_button 'wp-submit'
   
      expect(page).to have_content(/CSS Optimization/i)

      # deactivate
      find("tr[data-slug='css-optimization']").find('td.column-primary').find(".deactivate", match: :first).find("a").click

      expect(page).to have_content("Plugin deactivated.")

      # delete
      accept_confirm do
        find("tr[data-slug='css-optimization']").find('td.column-primary').find("a.delete").click
      end
  
      expect(page).to have_content("was successfully deleted.");

      visit "#{uri}/wp-admin/plugin-install.php?tab=upload"
 
      attach_file('pluginzip', File.absolute_path('/tmp/wordpress/wp-content/plugins/wordpress-css-optimization.zip'))

      click_button 'install-plugin-submit', match: :first

      expect(page).to have_content("Plugin installed successfully.");

      # activate
      find(".button-primary").click

      expect(page).to have_content("Plugin activated.");

    end

  end
 
end

# Check if command exists
def command?(name)
  `which #{name}`
  $?.success?
end