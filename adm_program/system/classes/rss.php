<?php
/******************************************************************************
 * RSS - Klasse
 *
 * Copyright    : (c) 2004 - 2013 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse erzeugt ein RSSfeed-Objekt nach RSS 2.0.
 *
 * Das Objekt wird erzeugt durch Aufruf des Konstruktors:
 * function RSSfeed($homepage, $title, $description)
 * Parameters:  $homepage       - Link zur Homepage
 *              $title          - Titel des RSS-Feeds
 *              $description    - Ergaenzende Beschreibung zum Titel
 *
 * Dem RSSfeed koennen ueber die Funktion addItem Inhalt zugeordnet werden:
 * function addItem($title, $description, $date, $guid)
 * Parameters:  $title          - Titel des Items
 *              $description    - der Inhalt des Items
 *              $date           - Das Erstellungsdatum des Items
 *              $link           - Ein Link zum Termin/Newsbeitrag etc.
 *
 * Wenn alle benoetigten Items zugeordnet sind, wird der RSSfeed generiert mit:
 * function buildFeed()
 *
 * Spezifikation von RSS 2.0: http://www.feedvalidator.org/docs/rss2.html
 *
 *****************************************************************************/


// RSS-Class
class RSSfeed
{

/** Constructor of the RSS class which needs all the information of the channel
 *  @param $title       Headline of this channel
 *  @param $link        Link to the website of this RSS feed
 *  @param $description Short description of this channel
 *  @param $copyright   Author of the channel; in our case the organization name
 */
public function __construct($title, $link, $description, $copyright)
{
    $this->channel = array();
    $this->channel['title'] = $title;
    $this->channel['link']  = $link;
    $this->channel['description'] = $description;
    $this->channel['copyright'] = $copyright;
    $this->items=array();
    $this->feed='http://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];
}

/** Add rss item to the current feed with all neccessary information about the item 
 *  @param $title       Headline of this item
 *  @param $description The main content of the item which can contain html
 *  @param $link        Link to this entry on the homepage
 *  @param $author      The name of the member who creates this entry
 *  @param $date        Publication date of this entry
 */
public function addItem($title, $description, $link, $author, $date)
{
    $item = array('title' => $title, 'description' => $description, 'author' => $author, 'pubDate' => $date, 'link' => $link);
    $this->items[]=$item;
}

public function buildFeed()
{
    $this->rssHeader();
    $this->openChannel();
    $this->addChannelInfos();
    $this->buildItems();
    $this->closeChannel();
    $this->rssFooter();
}

public function rssHeader()
{
    header('Content-type: application/xml');
    echo '<?xml version="1.0" encoding="utf-8"?>'. chr(10). '<rss version="2.0">'. chr(10);
}

public function openChannel()
{
    echo '<channel>'. chr(10);
}


public function addChannelInfos()
{
	global $gPreferences;

    foreach (array('title', 'link', 'description', 'copyright') as $field)
    {
        if (isset($this->channel[$field]))
        {
            echo "<${field}>". htmlspecialchars($this->channel[$field], ENT_QUOTES). "</${field}>\n";
        }
    }
    echo "<language>".$gPreferences['system_language']."</language>\n";
    echo "<generator>Admidio RSS-Class</generator>\n\n";
    echo "<pubDate>". date('r'). "</pubDate>\n\n";
}


public function buildItems()
{
    foreach ($this->items as $item)
    {
        echo "<item>\n";
        foreach (array('title', 'description', 'link', 'author', 'pubDate') as $field)
        {
            if (isset($item[$field]))
            {
                echo "<${field}>". htmlspecialchars($item[$field], ENT_QUOTES). "</${field}>\n";
            }
        }
        echo "<guid>". str_replace('&', '&amp;', $item['link']). "</guid>\n";
        echo '<source url="'.$this->feed.'">'. htmlspecialchars($this->channel['title'], ENT_QUOTES). "</source>\n";
        echo "</item>\n\n";
    }
}

public function closeChannel()
{
    echo '</channel>'. chr(10);
}

public function rssFooter()
{
    echo '</rss>'. chr(10);
}


} //Ende der Klasse

?>