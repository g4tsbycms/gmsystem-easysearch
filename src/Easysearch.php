<?php

namespace GMSystem\Easysearch;

/**
 * Class GMSystem Easysearch
 *
 * @author Genilson M. Souza <https://github.com/g4tsbycms>
 * @package GMSystem\Easysearch
 */
class Easysearch extends MetaTags
{
    /**
     * @param string $title
     * @param string $description
     * @param string $url
     * @param string $image
     * @param bool $follow
     * @return Easysearch
     */
    public function optimize(
        string $title,
        string $description,
        string $url,
        string $image,
        bool $follow = true
    ): Easysearch {
        $this->data($title, $description, $url, $image);

        $title = $this->filter($title);
        $description = $this->filter($description);

        $this->buildTag("title", $title);
        $this->buildMeta("name", ["description" => $description]);
        $this->buildMeta("name", ["robots" => ($follow ? "index, follow" : "noindex, nofollow")]);
        $this->buildLink("canonical", $url);

        foreach ($this->tags as $meta => $prefix) {
            $this->buildMeta(
                $meta,
                [
                    "{$prefix}:title" => $title,
                    "{$prefix}:description" => $description,
                    "{$prefix}:url" => $url,
                    "{$prefix}:image" => $image
                ]
            );
        }

        $this->buildMeta(
            "itemprop",
            [
                "name" => $title,
                "description" => $description,
                "url" => $url,
                "image" => $image
            ]
        );

        return $this;
    }

    /**
     * @param string $fbPage
     * @param string|null $fbAuthor
     * @return Easysearch
     */
    public function publisher(string $fbPage, string $fbAuthor = null): Easysearch
    {
        $this->buildMeta(
            "property",
            [
                "article:publisher" => "https://www.facebook.com/{$fbPage}"
            ]
        );

        if ($fbAuthor) {
            $this->buildMeta(
                "property",
                [
                    "article:author" => "https://www.facebook.com/{$fbAuthor}"
                ]
            );
        }

        return $this;
    }

    /**
     * @param string $siteName
     * @param string $locale
     * @param string $schema
     * @return Easysearch
     */
    public function openGraph(string $siteName, string $locale = "pt_BR", string $schema = "article"): Easysearch
    {
        $prefix = "og";
        $siteName = $this->filter($siteName);

        $this->buildMeta(
            "property",
            [
                "{$prefix}:type" => $schema,
                "{$prefix}:site_name" => $siteName,
                "{$prefix}:locale" => $locale
            ]
        );

        return $this;
    }

    /**
     * @param string $creator
     * @param string $site
     * @param string $domain
     * @param string|null $card
     * @return Easysearch
     */
    public function twitterCard(string $creator, string $site, string $domain, string $card = null): Easysearch
    {
        $prefix = "twitter";
        $card = ($card ?? "summary_large_image");

        $this->buildMeta(
            "name",
            [
                "{$prefix}:card" => $card,
                "{$prefix}:site" => $site,
                "{$prefix}:creator" => $creator,
                "{$prefix}:domain" => $domain
            ]
        );

        return $this;
    }

    /**
     * Você deve usar UM ou OUTRO, se for usar $appid deixe o $admins em null.
     * Mas se for usar $admins, então deixe o $appid em null.
     * @param string|null $appId
     * @param array|null $admins
     * @return Easysearch
     */
    public function facebook(string $appId = null, array $admins = null): Easysearch
    {
        if ($appId) {
            $fb = $this->meta->addChild("meta");
            $fb->addAttribute("property", "fb:app_id");
            $fb->addAttribute("content", $appId);
            return $this;
        }

        if (!empty($admins) && is_array($admins)) {
            foreach ($admins as $admin) {
                $fb = $this->meta->addChild("meta");
                $fb->addAttribute("property", "fb:admins");
                $fb->addAttribute("content", $admin);
            }
        }

        return $this;
    }
}
