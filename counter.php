<?php

/**
 * Counting who and how many from scraping drupal.org commit credit listings
 */

require __DIR__ . '/vendor/autoload.php';
use Sunra\PhpSimple\HtmlDomParser;


// Find credit info related to a given issue.
function credits_for_issue($url) {
  $issueDOM = HtmlDomParser::file_get_html('https://www.drupal.org/' . $url);
  $revision = FALSE;
  if (is_object($issueDOM)) {
    $revision = $issueDOM->find('a[class=revision]', 0);
  }
  else {
    return $url . ' is not returning an object';
  }
  if ($revision) {
    $revision_url = $revision->href;
    $commitDOM = HtmlDomParser::file_get_html('https://www.drupal.org/' . $revision_url)->find('div[class=view-vc-git-individual-commit] div[class=views-field-message] pre', 0);
    return $commitDOM->plaintext;
  }
  else {
    $last_page_comment = $issueDOM->find('li[class=pager-last]', 0);
    print_r($last_page_comment->plaintext);
    // TODO We don't have commit so we are on the wrong page jump to last page of comments.
  }
}

// Update following wit your orgs information
// TODO pass in org and project id as external variables
$dom = HtmlDomParser::file_get_html("https://www.drupal.org/node/ORGID/issue-credits/PROJECTID");

$last_page = $dom->find('li[class=pager-last] a', 0);
$num_pages = stristr($last_page->href, '?page=');
$num_pages = substr($num_pages, 6);

$count = 0;
while ($count <= $num_pages) {
  // URL Format drupal.org/node/ORGID/issue-credits/PROJECTID.
  // Update following with your orgs information
  $dom = HtmlDomParser::file_get_html("https://www.drupal.org/node/ORGID/issue-credits/PROJECTID?page=" . $count);

  $list_of_commits = $dom->find('div[class=view-id-issue_credit] ul li span[class=field-content] a');
  foreach ($list_of_commits as $key => $list_item) {
    print_r(credits_for_issue($list_item->href));
    print PHP_EOL;
  }
  $count++;
}
