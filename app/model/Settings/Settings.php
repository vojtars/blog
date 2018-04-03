<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;

/**
 * Settings
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity()
 */
class Settings
{

	Use Identifier;
	Use EntityValidator;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", nullable=true)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="title1", type="string", nullable=true)
	 */
	private $title1;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="title2", type="string", nullable=true)
	 */
	private $title2;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="url", type="string", nullable=true)
	 */
	private $url;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="company", type="string", nullable=true)
	 */
	private $company;

	/**
	 * @var Image
	 *
	 * @ORM\ManyToOne(targetEntity="Image")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
	 * })
	 */
	private $image;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="email", type="string", nullable=true)
	 */
	private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="phone", type="string", nullable=true)
	 */
	private $phone;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="street", type="string", nullable=true)
	 */
	private $street;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="city", type="string", nullable=true)
	 */
	private $city;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="zip", type="integer", nullable=true)
	 */
	private $zip;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="ico", type="integer", nullable=true)
	 */
	private $ico;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="facebook", type="string", nullable=true)
	 */
	private $facebook;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="twitter", type="string", nullable=true)
	 */
	private $twitter;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="instagram", type="string", nullable=true)
	 */
	private $instagram;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="linkedin", type="string", nullable=true)
	 */
	private $linkedIn;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="github", type="string", nullable=true)
	 */
	private $github;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_facebook", type="boolean", nullable=true)
	 */
	private $showFacebook;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_address", type="boolean", nullable=true)
	 */
	private $showAddress;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_map", type="boolean", nullable=true)
	 */
	private $showMap;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_twitter", type="boolean", nullable=true)
	 */
	private $showTwitter;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_linkedin", type="boolean", nullable=true)
	 */
	private $showLinkedIn;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_github", type="boolean", nullable=true)
	 */
	private $showGithub;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_instagram", type="boolean", nullable=true)
	 */
	private $showInstagram;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_comments", type="boolean", nullable=true)
	 */
	private $showComments;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_projects", type="boolean", nullable=true)
	 */
	private $showProjects;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_twitter_timeline", type="boolean", nullable=true)
	 */
	private $showTwitterTimeline;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="footer_text", type="string", nullable=true)
	 */
	private $footerText;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="head_title", type="string", nullable=true)
	 */
	private $headTitle;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="head_description", type="string", nullable=true)
	 */
	private $headDescription;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="head_keywords", type="string", nullable=true)
	 */
	private $headKeywords;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="send_new_subscribers", type="boolean", nullable=false)
	 */
	private $sendNewSubscribers;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="me_name", type="string", nullable=true)
	 */
	private $meName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="me_menu_name", type="string", nullable=true)
	 */
	private $meMenuName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="me_description", type="string", nullable=true)
	 */
	private $meDescription;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="me_content", type="string", nullable=true)
	 */
	private $meContent;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="fb_app_id", type="string", nullable=true)
	 */
	private $fbAppId;

	/**
	 * @var Image
	 *
	 * @ORM\ManyToOne(targetEntity="Image")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="me_image_id", referencedColumnName="id")
	 * })
	 */
	private $meImage;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="me_show_page", type="boolean", nullable=true)
	 */
	private $meShowPage;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="share_facebook", type="boolean", nullable=true)
	 */
	private $shareFacebook;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="share_twitter", type="boolean", nullable=true)
	 */
	private $shareTwitter;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="scripts_head", type="string", nullable=true)
	 */
	private $scriptsHead;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="scripts_footer", type="string", nullable=true)
	 */
	private $scriptsFooter;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="content_color", type="string", nullable=true)
	 */
	private $contentColor;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="maps_api_key", type="string", nullable=true)
	 */
	private $mapsApiKey;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="longitude", type="float", nullable=true)
	 */
	private $longitude;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="latitude", type="float", nullable=true)
	 */
	private $latitude;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="terms", type="string", nullable=true)
	 */
	private $terms;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="cta_own", type="boolean", nullable=true)
	 */
	private $ctaOwn;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="cta_href", type="string", nullable=true)
	 */
	private $ctaHref;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="cta_name", type="string", nullable=true)
	 */
	private $ctaName;

	/**
	 * @var Image
	 *
	 * @ORM\ManyToOne(targetEntity="Image")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="head_image_id", referencedColumnName="id")
	 * })
	 */
	private $headImage;


	/**
	 * @return string|null
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	public function getTitle1(): ?string
	{
		return $this->title1;
	}

	/**
	 * @return string|null
	 */
	public function getTitle2(): ?string
	{
		return $this->title2;
	}

	/**
	 * @return string|null
	 */
	public function getUrl(): ?string
	{
		return $this->url;
	}

	/**
	 * @return Image|null
	 */
	public function getImage(): ?Image
	{
		return $this->checkImage($this->image);
	}

	/**
	 * @return string|null
	 */
	public function getMeMenuName(): ?string
	{
		return $this->meMenuName;
	}

	/**
	 * @return string|null
	 */
	public function getEmail(): ?string
	{
		return $this->email;
	}

	/**
	 * @return string|null
	 */
	public function getPhone(): ?string
	{
		return $this->phone;
	}

	/**
	 * @return int|null
	 */
	public function getIco(): ?int
	{
		return $this->ico;
	}

	/**
	 * @return string|null
	 */
	public function getFacebook(): ?string
	{
		return $this->facebook;
	}

	/**
	 * @return float|NULL
	 */
	public function getLongitude(): ?float
	{
		return $this->longitude;
	}

	/**
	 * @return float|NULL
	 */
	public function getLatitude(): ?float
	{
		return $this->latitude;
	}

	/**
	 * @return string|null
	 */
	public function getTerms(): ?string
	{
		return $this->terms;
	}

	/**
	 * @return string|null
	 */
	public function getTwitter(): ?string
	{
		return $this->twitter;
	}

	/**
	 * @return string|null
	 */
	public function getInstagram(): ?string
	{
		return $this->instagram;
	}

	/**
	 * @return string|null
	 */
	public function getLinkedIn(): ?string
	{
		return $this->linkedIn;
	}

	/**
	 * @return string|null
	 */
	public function getGithub(): ?string
	{
		return $this->github;
	}

	/**
	 * @return string|NULL
	 */
	public function getCompany(): ?string
	{
		return $this->company;
	}

	/**
	 * @return string|NULL
	 */
	public function getMapsApiKey(): ?string
	{
		return $this->mapsApiKey;
	}

	/**
	 * @return bool
	 */
	public function isShowTwitterTimeline(): bool
	{
		return $this->showTwitterTimeline;
	}

	/**
	 * @return bool
	 */
	public function isShowFacebook(): bool
	{
		return $this->showFacebook;
	}

	/**
	 * @return bool
	 */
	public function isShowTwitter(): bool
	{
		return $this->showTwitter;
	}

	/**
	 * @return bool
	 */
	public function isShowLinkedIn(): bool
	{
		return $this->showLinkedIn;
	}

	/**
	 * @return string
	 */
	public function getContentColor(): string
	{
		return $this->contentColor;
	}

	/**
	 * @return bool
	 */
	public function isShowGithub(): bool
	{
		return $this->showGithub;
	}

	/**
	 * @return bool
	 */
	public function isShowInstagram(): bool
	{
		return $this->showInstagram;
	}

	/**
	 * @return bool
	 */
	public function isShowComments(): bool
	{
		return $this->showComments;
	}

	/**
	 * @return string|null
	 */
	public function getFooterText(): ?string
	{
		return $this->footerText;
	}

	/**
	 * @return string|null
	 */
	public function getHeadTitle(): ?string
	{
		return $this->headTitle;
	}

	/**
	 * @return string|null
	 */
	public function getHeadDescription(): ?string
	{
		return $this->headDescription;
	}

	/**
	 * @return bool
	 */
	public function isCtaOwn(): bool
	{
		return $this->ctaOwn;
	}

	/**
	 * @return string|NULL
	 */
	public function getCtaHref(): ?string
	{
		return $this->ctaHref;
	}

	/**
	 * @return string|NULL
	 */
	public function getCtaName(): ?string
	{
		return $this->ctaName;
	}

	/**
	 * @return string|null
	 */
	public function getHeadKeywords(): ?string
	{
		return $this->headKeywords;
	}

	/**
	 * @return Image|null
	 */
	public function getHeadImage(): ?Image
	{
		return $this->checkImage($this->headImage);
	}

	/**
	 * @return bool
	 */
	public function isShowProjects(): bool
	{
		return $this->showProjects;
	}

	/**
	 * @return bool
	 */
	public function isShareFacebook(): bool
	{
		return $this->shareFacebook;
	}

	/**
	 * @return bool
	 */
	public function isShareTwitter(): bool
	{
		return $this->shareTwitter;
	}

	/**
	 * @return string|null
	 */
	public function getFbAppId(): ?string
	{
		return $this->fbAppId;
	}

	/**
	 * @return string|NULL
	 */
	public function getScriptsHead(): ?string
	{
		return $this->scriptsHead;
	}

	/**
	 * @return string|NULL
	 */
	public function getScriptsFooter(): ?string
	{
		return $this->scriptsFooter;
	}

	/**
	 * @param bool $showProjects
	 */
	public function setShowProjects(bool $showProjects): void
	{
		$this->showProjects = $showProjects;
	}

	/**
	 * @return bool
	 */
	public function isSendNewSubscribers(): bool
	{
		return $this->sendNewSubscribers;
	}

	/**
	 * @return string|null
	 */
	public function getMeName(): ?string
	{
		return $this->meName;
	}

	/**
	 * @return string|NULL
	 */
	public function getStreet(): ?string
	{
		return $this->street;
	}

	/**
	 * @return string|NULL
	 */
	public function getCity(): ?string
	{
		return $this->city;
	}

	/**
	 * @return int|NULL
	 */
	public function getZip(): ?int
	{
		return $this->zip;
	}

	/**
	 * @return bool
	 */
	public function isShowAddress(): bool
	{
		return $this->showAddress;
	}

	/**
	 * @return bool
	 */
	public function isShowMap(): bool
	{
		return $this->showMap;
	}

	/**
	 * @return string|null
	 */
	public function getMeDescription(): ?string
	{
		return $this->meDescription;
	}

	/**
	 * @return string|null
	 */
	public function getMeContent(): ?string
	{
		return $this->meContent;
	}

	/**
	 * @return Image|null
	 */
	public function getMeImage(): ?Image
	{
		return $this->checkImage($this->meImage);
	}

	/**
	 * @return bool
	 */
	public function isMeShowPage(): bool
	{
		return $this->meShowPage;
	}

	/**
	 * @param string $meName
	 */
	public function setMeName(string $meName): void
	{
		$this->meName = $meName;
	}

	/**
	 * @param string $meDescription
	 */
	public function setMeDescription(string $meDescription): void
	{
		$this->meDescription = $meDescription;
	}

	/**
	 * @param string $meContent
	 */
	public function setMeContent(string $meContent): void
	{
		$this->meContent = $meContent;
	}

	/**
	 * @param Image $meImage
	 */
	public function setMeImage(Image $meImage): void
	{
		$this->meImage = $meImage;
	}

	/**
	 * @param bool $meShowPage
	 */
	public function setMeShowPage(bool $meShowPage): void
	{
		$this->meShowPage = $meShowPage;
	}

	/**
	 * @param bool $showFacebook
	 */
	public function setShowFacebook(bool $showFacebook): void
	{
		$this->showFacebook = $showFacebook;
	}

	/**
	 * @param bool $showTwitter
	 */
	public function setShowTwitter(bool $showTwitter): void
	{
		$this->showTwitter = $showTwitter;
	}

	/**
	 * @param bool $showLinkedIn
	 */
	public function setShowLinkedIn(bool $showLinkedIn): void
	{
		$this->showLinkedIn = $showLinkedIn;
	}

	/**
	 * @param bool $showGithub
	 */
	public function setShowGithub(bool $showGithub): void
	{
		$this->showGithub = $showGithub;
	}

	/**
	 * @param bool $showInstagram
	 */
	public function setShowInstagram(bool $showInstagram): void
	{
		$this->showInstagram = $showInstagram;
	}

	/**
	 * @param bool $showComments
	 */
	public function setShowComments(bool $showComments): void
	{
		$this->showComments = $showComments;
	}

	/**
	 * @param bool $showTwitterTimeline
	 */
	public function setShowTwitterTimeline(bool $showTwitterTimeline): void
	{
		$this->showTwitterTimeline = $showTwitterTimeline;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @param string $title1
	 */
	public function setTitle1(string $title1): void
	{
		$this->title1 = $title1;
	}

	/**
	 * @param string $title2
	 */
	public function setTitle2(string $title2): void
	{
		$this->title2 = $title2;
	}

	/**
	 * @param string $url
	 */
	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

	/**
	 * @param Image $image
	 */
	public function setImage(Image $image): void
	{
		$this->image = $image;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone(string $phone): void
	{
		$this->phone = $phone;
	}

	/**
	 * @param int $ico
	 */
	public function setIco(int $ico): void
	{
		$this->ico = $ico;
	}

	/**
	 * @param string $footerText
	 */
	public function setFooterText(string $footerText): void
	{
		$this->footerText = $footerText;
	}

	/**
	 * @param string $facebook
	 */
	public function setFacebook(string $facebook): void
	{
		$this->facebook = $facebook;
	}

	/**
	 * @param string $twitter
	 */
	public function setTwitter(string $twitter): void
	{
		$this->twitter = $twitter;
	}

	/**
	 * @param string $instagram
	 */
	public function setInstagram(string $instagram): void
	{
		$this->instagram = $instagram;
	}

	/**
	 * @param string $linkedIn
	 */
	public function setLinkedIn(string $linkedIn): void
	{
		$this->linkedIn = $linkedIn;
	}

	/**
	 * @param string $github
	 */
	public function setGithub(string $github): void
	{
		$this->github = $github;
	}

	/**
	 * @param string $headTitle
	 */
	public function setHeadTitle(string $headTitle): void
	{
		$this->headTitle = $headTitle;
	}

	/**
	 * @param string $headDescription
	 */
	public function setHeadDescription(string $headDescription): void
	{
		$this->headDescription = $headDescription;
	}

	/**
	 * @param string $headKeywords
	 */
	public function setHeadKeywords(string $headKeywords): void
	{
		$this->headKeywords = $headKeywords;
	}

	/**
	 * @param Image $headImage
	 */
	public function setHeadImage(Image $headImage): void
	{
		$this->headImage = $headImage;
	}

	/**
	 * @param bool $sendNewSubscribers
	 */
	public function setSendNewSubscribers(bool $sendNewSubscribers): void
	{
		$this->sendNewSubscribers = $sendNewSubscribers;
	}

	/**
	 * @param bool $shareFacebook
	 */
	public function setShareFacebook(bool $shareFacebook): void
	{
		$this->shareFacebook = $shareFacebook;
	}

	/**
	 * @param bool $shareTwitter
	 */
	public function setShareTwitter(bool $shareTwitter): void
	{
		$this->shareTwitter = $shareTwitter;
	}

	/**
	 * @param string $fbAppId
	 */
	public function setFbAppId(string $fbAppId): void
	{
		$this->fbAppId = $fbAppId;
	}

	/**
	 * @param string $scriptsHead
	 */
	public function setScriptsHead(string $scriptsHead): void
	{
		$this->scriptsHead = $scriptsHead;
	}

	/**
	 * @param string $scriptsFooter
	 */
	public function setScriptsFooter(string $scriptsFooter): void
	{
		$this->scriptsFooter = $scriptsFooter;
	}

	/**
	 * @param string $contentColor
	 */
	public function setContentColor(string $contentColor): void
	{
		$this->contentColor = $contentColor;
	}

	/**
	 * @param string $street
	 */
	public function setStreet(string $street): void
	{
		$this->street = $street;
	}

	/**
	 * @param string $city
	 */
	public function setCity(string $city): void
	{
		$this->city = $city;
	}

	/**
	 * @param int $zip
	 */
	public function setZip(int $zip): void
	{
		$this->zip = $zip;
	}

	/**
	 * @param bool $showAddress
	 */
	public function setShowAddress(bool $showAddress): void
	{
		$this->showAddress = $showAddress;
	}

	/**
	 * @param bool $showMap
	 */
	public function setShowMap(bool $showMap): void
	{
		$this->showMap = $showMap;
	}

	/**
	 * @param string $mapsApiKey
	 */
	public function setMapsApiKey(string $mapsApiKey): void
	{
		$this->mapsApiKey = $mapsApiKey;
	}

	/**
	 * @param string|NULL $company
	 */
	public function setCompany(?string $company): void
	{
		$this->company = $company;
	}

	/**
	 * @param float $longitude
	 */
	public function setLongitude(float $longitude): void
	{
		$this->longitude = $longitude;
	}

	/**
	 * @param float $latitude
	 */
	public function setLatitude(float $latitude): void
	{
		$this->latitude = $latitude;
	}

	/**
	 * @param string $terms
	 */
	public function setTerms(string $terms): void
	{
		$this->terms = $terms;
	}

	/**
	 * @param bool $ctaOwn
	 */
	public function setCtaOwn(bool $ctaOwn): void
	{
		$this->ctaOwn = $ctaOwn;
	}

	/**
	 * @param string $ctaHref
	 */
	public function setCtaHref(string $ctaHref): void
	{
		$this->ctaHref = $ctaHref;
	}

	/**
	 * @param string $ctaName
	 */
	public function setCtaName(string $ctaName): void
	{
		$this->ctaName = $ctaName;
	}

	/**
	 * @param string|NULL $meMenuName
	 */
	public function setMeMenuName(?string $meMenuName): void
	{
		$this->meMenuName = $meMenuName;
	}


}
