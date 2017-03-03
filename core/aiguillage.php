<?php
/**
*
* @package phpBB Extension - LMDI Indexing extension
* @copyright (c) 2016 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace lmdi\index\core;

class aiguillage
{
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\extension\manager "Extension Manager" */
	protected $ext_manager;
	/** @var \phpbb\path_helper */
	protected $path_helper;
	/** @var \lmdi\gloss\core\helper */
	protected $gloss_helper;
	// Strings
	protected $phpEx;
	protected $phpbb_root_path;
	protected $table_rh_tt;
	protected $table_rh_t;
	protected $ext_path;
	protected $ext_path_web;

	protected $petits_ordres = array (
	"Embioptera", "Phthiraptera", "Psocoptera", "Thysanoptera"
	);

	// Tous ces tableaux doivent être un multiple de 4.

	protected $Animalia = array (
	"Insecta", "Arachnida", "Chilopoda", "Crustacea",
	"Diplopoda", "Entognatha", /*"Merostomata", "Pauropoda",*/ "Pycnogonida", "Symphyla",
	"Tardigrada");

	protected $Insecta = array (
	"Coleoptera", "Dermaptera", "Dictyoptera", "Diptera",
	"Embioptera", "Ephemeroptera", "Heteroptera", "Homoptera",
	"Hymenoptera", "Lepidoptera", "Mantophasmatodea", "Mecoptera",
	"Megaloptera", "Microcoryphia", "Neuroptera", "Odonata",
	"Orthoptera", "Phasmatodea", "Phthiraptera", "Plecoptera",
	"Psocoptera", "Raphidioptera", "Siphonaptera", "Strepsiptera",
	"Thysanoptera", "Trichoptera", "Zygentoma", "");
	protected $Coleoptera = array (
	"Aderidae", "Aegialiidae", "Agyrtidae", "Alexiidae",
	"Anobiidae", "Anthicidae", "Anthribidae", "Anthribidae",
	"Aphodiidae", "Apionidae", "Attelabidae", "Biphyllidae",
	"Bolboceratidae", "Bostrichidae", "Bothrideridae", "Brachyceridae",
	"Brentidae", "Buprestidae", "Byrrhidae", "Byturidae",
	"Cantharidae", "Carabidae", "Cerambycidae", "Ceratocanthidae",
	"Cerophytidae", "Cerylonidae", "Cetoniidae", "Chironidae",
	"Chrysomelidae", "Ciidae", "Cleridae", "Coccinellidae",
	"Corylophidae", "Cryptophagidae", "Cucujidae", "Curculionidae",
	"Cybocephalidae", "Dascillidae", "Dasytidae", "Dermestidae",
	"Derodontidae", "Discolomatidae", "Drilidae", "Dryophthoridae",
	"Dryopidae", "Dynastidae", "Dytiscidae", "Elateridae", "Elmidae",
	"Endecatomidae", "Endomychidae", "Erirhinidae", "Erotylidae",
	"Eucinetidae", "Eucnemidae", "Geotrupidae", "Glaphyridae",
	"Glaresidae", "Gyrinidae", "Haliplidae", "Helotidae",
	"Heteroceridae", "Histeridae", "Hybosoridae", "Hydraenidae",
	"Hydrophilidae", "Hygrobiidae", "Kateretidae", "Laemophloeidae",
	"Lampyridae", "Languriidae", "Latridiidae", "Leiodidae",
	"Limnichidae", "Lucanidae", "Lycidae", "Lyctidae",
	"Lymexylidae", "Malachiidae", "Melandryidae", "Meloidae",
	"Melolonthidae", "Melyridae", "Monotomidae", "Mordellidae",
	"Mycetophagidae", "Mycteridae", "Nanophyidae", "Nemonychidae",
	"Nilionidae", "Nitidulidae", "Nosodendridae", "Noteridae",
	"Ochodaeidae", "Oedemeridae", "Omalisidae", "Pachypodidae",
	"Passalidae", "Passandridae", "Phalacridae", "Phloeostichidae",
	"Phloiophilidae", "Prionoceridae", "Prostomidae", "Ptiliidae",
	"Pyrochroidae", "Pythidae", "Raymondionymidae", "Rhipiceridae",
	"Rhynchitidae", "Ripiphoridae", "Rutelidae", "Salpingidae",
	"Scarabaeidae", "Scirtidae", "Scraptiidae", "Scydmaenidae",
	"Silphidae", "Silvanidae", "Sphaeritidae", "Sphindidae",
	"Staphylinidae", "Tenebrionidae", "Tetratomidae", "Throscidae",
	"Trictenotomidae", "Trogidae", "Trogositidae", "Zopheridae");
	protected $Dermaptera = array (
	"Anisolabididae", "Apachyidae", "Chelisochidae", "Forficulidae",
	"Labiduridae", "Pygidicranidae", "Spongiphoridae", "");
	protected $Dictyoptera = array (
	"Acanthopidae", "Amorphoscelididae", "Blaberidae", "Blattellidae",
	"Blattidae", "Corydiidae", "Empusidae", "Eremiaphilidae",
	"Hymenopodidae", "Iridopterigidae", "Kalotermitidae", "Mantidae",
	"Polyphagidae", "Rhinotermitidae", "Tarachodidae", "Termitidae");
	protected $Diptera = array (
	"Acroceridae", "Agromyzidae", "Anisopodidae", "Anthomyiidae",
	"Asilidae", "Asteiidae", "Athericidae", "Aulacigastridae",
	"Bibionidae", "Blephariceridae", "Bolitophilidae", "Bombyliidae",
	"Brachystomatidae", "Braulidae", "Calliphoridae", "Camillidae",
	"Campichoetidae", "Carnidae", "Cecidomyiidae", "Ceratopogonidae",
	"Chamaemyiidae", "Chaoboridae", "Chironomidae", "Chloropidae",
	"Chyromyidae", "Clusiidae", "Coelopidae", "Coenomyiidae",
	"Conopidae", "Culicidae", "Cylindrotomidae", "Ditomyiidae",
	"Dixidae", "Dolichopodidae", "Drosophilidae", "Dryomyzidae",
	"Empididae", "Ephydridae", "Fanniidae", "Helcomyzidae",
	"Heleomyzidae", "Hippoboscidae", "Hybotidae", "Keroplatidae",
	"Lauxaniidae", "Limoniidae", "Lonchaeidae", "Lonchopteridae",
	"Megamerinidae", "Micropezidae", "Microphoridae", "Milichiidae",
	"Muscidae", "Mycetophilidae", "Mydidae", "Nemestrinidae",
	"Neriidae", "Nycteribiidae", "Odiniidae", "Oestridae",
	"Opomyzidae", "Pallopteridae", "Pediciidae", "Periscelididae",
	"Phaeomyiidae", "Phoridae", "Pipunculidae", "Platypezidae",
	"Platystomatidae", "Psilidae", "Psychodidae", "Ptychopteridae",
	"Rhagionidae", "Rhinophoridae", "Richardiidae", "Sarcophagidae",
	"Scathophagidae", "Scatopsidae", "Scenopinidae", "Sciaridae",
	"Sciomyzidae", "Sepsidae", "Simuliidae", "Sphaeroceridae",
	"Stratiomyidae", "Syrphidae", "Tabanidae", "Tachinidae",
	"Tanypezidae", "Tephritidae", "Tethinidae", "Therevidae",
	"Tipulidae", "Trichoceridae", "Trixoscelididae", "Ulidiidae",
	"Vermileonidae", "Xylomyidae", "Xylophagidae", "");
	protected $Embioptera = array ("Embiidae", "Oligotomidae", "", ""
	);
	protected $Ephemeroptera = array (
	"Ameletidae", "Baetidae", "Caenidae", "Ephemerellidae",
	"Ephemeridae", "Heptageniidae", "Leptophlebiidae", "Oligoneuriidae",
	"Polymitarcyidae", "Potamanthidae", "Prosopistomatidae", ""
	);
	protected $Heteroptera = array (
	"Acanthosomatidae", "Alydidae", "Anthocoridae", "Aphelocheiridae",
	"Aradidae", "Belostomatidae", "Berytidae", "Cimicidae", "Coreidae",
	"Corimelaenidae", "Corixidae", "Cydnidae", "Dinidoridae",
	"Dipsocoridae", "Gerridae", "Hebridae", "Hydrometridae",
	"Largidae", "Leptopodidae", "Lygaeidae", "Mesoveliidae",
	"Microphysidae", "Miridae", "Nabidae", "Naucoridae",
	"Nepidae", "Notonectidae", "Ochteridae", "Pentatomidae",
	"Piesmatidae", "Plataspididae", "Pleidae", "Pyrrhocoridae",
	"Reduviidae", "Rhopalidae", "Saldidae", "Scutelleridae",
	"Stenocephalidae", "Tessaratomidae", "Thaumastocoridae", "Thyreocoridae",
	"Tingidae", "Veliidae", "", ""
	);
	protected $Homoptera = array (
	"Achilidae", "Adelgidae", "Aleyrodidae", "Aphididae",
	"Aphrophoridae", "Caliscelidae", "Cercopidae", "Cicadellidae",
	"Cicadidae", "Cixiidae", "Coccidae", "Delphacidae",
	"Derbidae", "Diaspididae", "Dictyopharidae", "Eurybrachidae",
	"Flatidae", "Fulgoridae", "Homotomidae", "Issidae",
	"Margarodidae", "Membracidae", "Ortheziidae", "Phylloxeridae",
	"Pseudococcidae", "Psyllidae", "Ricaniidae", "Tettigometridae",
	"Tibicinidae", "Triozidae", "", ""
	);
	protected $Hymenoptera = array (
	"Ampulicidae", "Andrenidae", "Aphelinidae", "Apidae",
	"Argidae", "Aulacidae", "Bethylidae", "Braconidae",
	"Cephidae", "Ceraphronidae", "Chalcididae", "Chrysididae",
	"Cimbicidae", "Colletidae", "Crabronidae", "Cynipidae",
	"Diapriidae", "Diprionidae", "Dryinidae", "Embolemidae",
	"Encyrtidae", "Eucharitidae", "Eulophidae", "Eupelmidae",
	"Eurytomidae", "Evaniidae", "Figitidae", "Formicidae",
	"Gasteruptiidae", "Halictidae", "Heloridae", "Ibaliidae",
	"Ichneumonidae", "Leucospidae", "Megachilidae", "Megalodontesidae",
	"Megaspilidae", "Melittidae", "Mutillidae", "Mymaridae",
	"Ormyridae", "Pamphiliidae", "Pelecinidae", "Perilampidae",
	"Platygastridae", "Pompilidae", "Proctotrupidae", "Pteromalidae",
	"Sapygidae", "Scelionidae", "Scoliidae", "Siricidae",
	"Sphecidae", "Stephanidae", "Tenthredinidae", "Tetracampidae",
	"Tiphiidae", "Torymidae", "Trigonalidae", "Vespidae",
	"Xiphydriidae", "Xyelidae", "", ""
	);
	protected $Lepidoptera = array (
	"Acrolepiidae", "Acrolophidae", "Adelidae", "Agonoxenidae",
	"Alucitidae", "Anthelidae", "Apatelodidae", "Arctiidae",
	"Argyresthiidae", "Autostichidae", "Batrachedridae", "Bedelliidae",
	"Blastobasidae", "Bombycidae", "Brachodidae", "Brahmaeidae",
	"Bucculatricidae", "Castniidae", "Chimabachidae", "Choreutidae",
	"Cimeliidae", "Coleophoridae", "Cosmopterigidae", "Cossidae",
	"Crambidae", "Douglasiidae", "Drepanidae", "Elachistidae",
	"Endromidae", "Epermeniidae", "Erebidae", "Eriocraniidae",
	"Eupterotidae", "Euteliidae", "Gelechiidae", "Geometridae",
	"Glyphipterigidae", "Gracillariidae", "Heliodinidae", "Heliozelidae",
	"Hepialidae", "Hesperiidae","Heterogynidae", "Incurprotectediidae",
	"Lasiocampidae", "Lecithoceridae", "Limacodidae", "Lycaenidae",
	"Lyonetiidae", "Lypusidae", "Megalopygidae", "Micropterigidae",
	"Momphidae", "Nepticulidae", "Noctuidae", "Nolidae",
	"Notodontidae", "Nymphalidae", "Oecophoridae", "Opostegidae",
	"Papilionidae", "Peleopodidae", "Pieridae", "Plutellidae",
	"Praydidae", "Prodoxidae", "Psychidae", "Pterolonchidae",
	"Pterophoridae", "Pyralidae", "Riodinidae", "Saturniidae",
	"Schreckensteiniidae", "Scythrididae", "Sesiidae", "Somabrachyidae",
	"Sphingidae", "Stathmopodidae", "Thyrididae", "Tineidae",
	"Tischeriidae", "Tortricidae", "Uraniidae", "Urodidae",
	"Yponomeutidae", "Ypsolophidae", "Zygaenidae", ""
	);
	protected $Mantophasmatodea = array ("Mantophasmatidae", "", "", ""
	);
	protected $Mecoptera = array ("Bittacidae", "Boreidae", "Panorpidae", ""
	);
	protected $Megaloptera = array ("Corydalidae", "Sialidae", "", ""
	);
	protected $Microcoryphia = array ("Machilidae", "Meinertellidae", "", ""
	);
	protected $Neuroptera = array (
	"Ascalaphidae", "Berothidae", "Chrysopidae", "Coniopterygidae",
	"Dilaridae", "Hemerobiidae", "Mantispidae", "Myrmeleontidae",
	"Nemopteridae", "Osmylidae", "Sisyridae", ""
	);
	protected $Odonata = array (
	"Aeschnidiidae", "Aeshnidae", "Calopterygidae", "Chlorocyphidae",
	"Coenagrionidae", "Cordulegastridae", "Corduliidae", "Dicteriadidae",
	"Epiophlebiidae", "Euphaeidae", "Gomphidae", "Lestidae",
	"Libellulidae", "Macromiidae", "Megapodagrionidae", "Neopetaliidae",
	"Perilestidae", "Platycnemididae", "Polythoridae", "Protoneuridae",
	"Pseudostigmatidae", "", "", ""
	);
	protected $Orthoptera = array (
	"Acrididae", "Baissogryllidae", "Bradyporidae", "Chorotypidae",
	"Conocephalidae", "Gryllacrididae", "Gryllidae", "Gryllotalpidae",
	"Meconematidae", "Mogoplistidae", "Myrmecophilidae", "Pamphagidae",
	"Phaneropteridae", "Proscopiidae", "Pyrgomorphidae", "Rhaphidophoridae",
	"Romaleidae", "Schizodactylidae", "Tetrigidae", "Tettigoniidae",
	"Tridactylidae", "Trigonopterygidae", "", ""
	);
	protected $Phasmatodea = array (
	"Anareolatae", "Anisacanthidae", "Bacillidae", "Diapheromeridae",
	"Heteronemiidae", "Heteropterygidae", "Phasmatidae", "Phylliidae",
	"Pseudophasmatidae", "", "", ""
	);
	protected $Phthiraptera = array (
	"Bovicoliidae", "Haematopinidae", "Menoponidae", "Pediculidae",
	"Philopteridae", "Polyplacidae", "Pthiridae", "Trichodectidae"
	);
	protected $Plecoptera = array (
	"Capniidae", "Chloroperlidae", "Leuctridae", "Nemouridae",
	"Perlidae", "Perlodidae", "Taeniopterygidae", ""
	);
	protected $Psocoptera = array (
	"Amphientomidae", "Caeciliusidae", "Ectopsocidae", "Elipsocidae",
	"Epipsocidae", "Lachesillidae", "Lepidopsocidae", "Liposcelididae",
	"Mesopsocidae", "Myopsocidae", "Peripsocidae", "Philotarsidae",
	"Psocidae", "Psyllipsocidae", "Stenopsocidae", "Trichopsocidae",
	"Trogiidae", "", "", ""
	);
	protected $Raphidioptera = array ("Inocelliidae", "Raphidiidae", "", ""
	);
	protected $Siphonaptera = array ("Ceratophyllidae", "Pulicidae", "", ""
	);
	protected $Strepsiptera = array ("Xenidae", "", "", ""
	);
	protected $Thysanoptera = array ("Aeolothripidae", "Phlaeothripidae", "Thripidae", ""
	);
	protected $Trichoptera = array (
	"Apataniidae", "Goeridae", "Helicopsychidae", "Hydropsychidae",
	"Hydroptilidae", "Leptoceridae", "Limnephilidae", "Odontoceridae",
	"Philopotamidae", "Phryganeidae", "Polycentropodidae", "Psychomyiidae",
	"Rhyacophilidae", "Sericostomatidae", "", ""
	);
	protected $Zygentoma  = array ("Lepismatidae", "Nicoletiidae", "", ""
	);

	protected $Arachnida = array (
	"Amblypygi", "Araneae", "Astigmata", "Ixodida",
	"Mesostigmata", "Opiliones", "Oribatida", "Prostigmata",
	"Pseudoscorpiones", "Ricinulei", "Scorpiones", "Solifugae",
	"Uropygi", "", "", ""
	);
	protected $Amblypygi = array ("Phrynichidae", "Phrynidae", "", ""
	);
	protected $Araneae = array (
	"Agelenidae", "Amaurobiidae", "Anapidae", "Anyphaenidae",
	"Araneidae", "Atypidae", "Clubionidae", "Corinnidae",
	"Ctenidae", "Ctenizidae", "Cybaeidae", "Cyrtaucheniidae",
	"Deinopidae", "Desidae", "Dictynidae", "Dysderidae",
	"Eresidae", "Filistatidae", "Gnaphosidae", "Hahniidae",
	"Hersiliidae", "Hexathelidae", "Leptonetidae", "Linyphiidae",
	"Liocranidae", "Lycosidae", "Mimetidae", "Miturgidae",
	"Mysmenidae", "Nemesiidae", "Nephilidae", "Nesticidae",
	"Oecobiidae", "Oonopidae", "Oxyopidae", "Palpimanidae",
	"Philodromidae", "Pholcidae", "Pimoidae", "Pisauridae",
	"Psechridae", "Salticidae", "Scytodidae", "Segestriidae",
	"Sicariidae", "Sparassidae", "Synaphridae", "Tetragnathidae",
	"Theraphosidae", "Theridiidae", "Theridiosomatidae", "Thomisidae",
	"Titanoecidae", "Uloboridae", "Zodariidae", "Zoridae",
	"Zoropsidae", "", "", ""
	);
	protected $Astigmata = array (
	"Acaridae", "Chaetodactylidae", "Histiostomatidae", "Listrophoridae",
	"Psoroptidae", "Sarcoptidae", "Trouessartiidae", ""
	);
	protected $Ixodida = array ("Amblyommidae", "Argasidae", "Ixodidae", ""
	);
	protected $Mesostigmata = array (
	"Dermanyssidae", "Macrochelidae", "Macronyssidae", "Metagynuridae",
	"Parasitidae", "Podocinidae", "Polyaspididae", "Sejidae",
	"Trematuridae", "Urodinychidae", "Uropodidae", "Varroidae",
	"Veigaiidae", "Zerconidae", "", ""
	);
	protected $Opiliones = array (
	"Gonyleptidae", "Ischyropsalididae", "Ischyropsalididae", "Nemastomatidae",
	"Phalangiidae", "Phalangodidae", "Sabaconidae", "Sclerosomatidae",
	"Sironidae", "Travuniidae", "Trogulidae", ""
	);
	protected $Oribatida = array (
	"Camisiidae", "Cymbaeremaeidae", "Damaeidae", "Euphthiracaridae",
	"Humerobatidae", "Liacaridae", "Metrioppiidae", "Nothridae",
	"Phenopelopidae", "", "", ""
	);
	protected $Prostigmata = array (
	"Anystidae", "Arrenuridae", "Bdellidae", "Calyptostomatidae",
	"Ereynetidae", "Eriophyidae", "Erythraeidae", "Eupodidae",
	"Eylaidae", "Hydrachnidae", "Myobiidae", "Penthaleidae",
	"Pterygosomatidae", "Tetranychidae", "Trombiculidae", "Trombidiidae",
	"Unionicolidae", "", "", ""
	);
	protected $Pseudoscorpiones = array (
	"Cheiridiidae", "Cheliferidae", "Chernetidae", "Chthoniidae",
	"Neobisiidae", "", "", ""
	);
	protected $Ricinulei = array ("Ricinoididae", "", "", "");
	protected $Scorpiones = array (
	"Buthidae", "Chactidae", "Euscorpiidae", "Iuridae",
	"Scorpionidae", "Troglotayosicidae", "", "");
	protected $Solifugae = array ("Daesiidae", "Galeodidae", "", "");
	protected $Uropygi = array ("Thelyphonidae", "", "", "");

	protected $Chilopoda = array (
	"Geophilomorpha", "Lithobiomorpha", "Scolopendromorpha", "Scutigeromorpha");
	protected $Geophilomorpha = array (
	"Dignathodontidae", "Geophilidae", "Himantariidae", "Linotaeniidae",
	"Schendylidae", "", "", "");
	protected $Lithobiomorpha = array ("Henicopidae", "Lithobiidae", "", "");
	protected $Scolopendromorpha = array ("Cryptopidae", "Scolopendridae", "", "");
	protected $Scutigeromorpha = array ("Scutigeridae", "", "", "");

	protected $Crustacea = array (
	"Amphipoda", "Branchiopoda", "Decapoda", "Isopoda",
	"Maxillopoda", "Mysida", "Ostracoda", /* "Sessilia",*/ "Syncarida");
	protected $Amphipoda = array (
	"Caprellidae", "Corophidae", "Gammaridae", "Hyperiidae",
	"Niphargidae", "Talitridae", "", "");
	protected $Branchiopoda = array (
	"Artemiidae", "Branchipodidae", "Chirocephalidae", "Daphniidae",
	"Leptodoridae", "Triopsidae", "", "");
	protected $Decapoda = array (
	"Alpheidae", "Astacidae", "Atelecyclidae", "Atyidae",
	"Cambaridae", "Cancridae", "Carcinidae", "Coenobitidae",
	"Crangonidae", "Diogenidae", "Epialtidae", "Galatheidae",
	"Gecarcinidae", "Grapsidae", "Hippolytidae", "Hymenosomatidae",
	"Inachidae", "Majidae", "Nephropidae", "Ocypodidae",
	"Paguridae", "Palaemonidae", "Panopeidae", "Pilumnidae",
	"Polybiidae", "Porcellanidae", "Raninidae", "Scyllaridae",
	"Upogebiidae", "Varunidae", "Xanthidae", "");
	protected $Isopoda = array (
	"Agnaridae", "Arcturidae", "Armadillidae", "Armadillidiidae",
	"Asellidae", "Balloniscidae", "Bopyridae", "Cirolanidae",
	"Cylisticidae", "Cymothoidae", "Idoteidae", "Janiridae",
	"Ligiidae", "Oniscidae", "Philosciidae", "Platyarthridae",
	"Porcellionidae", "Scyphacidae", "Sphaeromatidae", "Stenasellidae",
	"Stenoniscidae", "Tendosphaeridae", "Trachelipodidae", "Trichoniscidae",
	"Tylidae", "", "", "");
	protected $Maxillopoda = array (
	"Austrobalanidae", "Balanidae", "Calanticidae", "Chthamalidae",
	"Cyclopidae", "Pollicipedidae", "Porcellidiidae", "Sacculinidae",
	"Verrucidae", "", "", "");
	protected $Mysida = array ("Mysidae", "", "", "");
	protected $Ostracoda = array ("Cyprididae", "", "", "");
	// protected $Sessilia = array ();
	protected $Syncarida = array ("Bathynellidae", "", "", "");

	protected $Diplopoda = array (
	"Callipodida", "Chordeumatida", "Glomerida", "Julida",
	/*"Juliformia",*/
	"Polydesmida", "Polyxenida", "Polyzoniida", /*"Sphaerotheriida",*/ "Spirobolida",
	"Spirostreptida", "", "", "");
	protected $Callipodida = array ("Callipodidae", "", "", "");
	protected $Chordeumatida = array (
	"Anthogonidae", "Brachychaeteumatidae", "Chamaesomatidae", "Chordeumatidae",
	"Craspedosomatidae", "Haaseidae", "", "");
	protected $Glomerida = array ("Glomeridae", "Protoglomeridae", "", "");
	protected $Julida = array ("Blaniulidae", "Julidae", "Nemasomatidae", "");
	// protected $Juliformia = array ();
	protected $Polydesmida = array ("Macrosternodesmidae", "Paradoxosomatidae", "Polydesmidae", "Xystodesmidae");
	protected $Polyxenida = array ("Lophoproctidae", "Polyxenidae", "", "");
	protected $Polyzoniida = array ("Hirudisomatidae", "Polyzoniidae", "", "");
	// protected $Sphaerotheriida = array ();
	protected $Spirobolida = array ("Pachybolidae", "Rhinocricidae", "Trigoniulidae", "");
	protected $Spirostreptida = array ("Spirostreptidae", "", "", "");

	protected $Entognatha = array ("Collembola", "Diplura", "", "");
	protected $Collembola = array (
	"Bourletiellidae", "Brachystomellidae", "Dicyrtomidae", "Entomobryidae",
	"Hypogastruridae", "Isotomidae", "Katiannidae", "Neanuridae",
	"Onychiuridae", "Poduridae", "Sminthuridae", "Sminthurididae",
	"Tomoceridae", "", "", "");
	protected $Diplura = array ("Campodeidae", "", "", "");

	// protected $Merostomata = array ("Xiphosura");
	// protected $Xiphosura = array ();

	// protected $Pauropoda = array ("Tetramerocerata");
	// protected $Tetramerocerata = array ();

	protected $Pycnogonida = array ("Pantopoda", "", "", "");
	protected $Pantopoda = array ("Ammotheidae", "Nymphonidae", "Phoxichilidiidae", "");

	protected $Symphyla = array ("_Symphyla", "", "", "");
	protected $_Symphyla = array ("Scutigerellidae", "", "", "");

	protected $Tardigrada = array ("Parachaela", "", "", "");
	protected $Parachaela = array ("Hypsibiidae", "", "", "");
	/**
	* Constructor
	*
	*/
	public function __construct(
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\auth\auth $auth,
		\phpbb\extension\manager $ext_manager,
		\phpbb\path_helper $path_helper,
		$phpEx,
		$phpbb_root_path,
		$table_rh_tt,
		$table_rh_t)
	{
		$this->template 		= $template;
		$this->user			= $user;
		$this->db				= $db;
		$this->config			= $config;
		$this->helper			= $helper;
		$this->auth			= $auth;
		$this->ext_manager		= $ext_manager;
		$this->path_helper		= $path_helper;
		$this->phpEx			= $phpEx;
		$this->phpbb_root_path	= $phpbb_root_path;
		$this->table_rh_tt		= $table_rh_tt;
		$this->table_rh_t		= $table_rh_t;

		$this->ext_path = $this->ext_manager->get_extension_path('lmdi/index', true);
		$this->ext_path_web = $this->path_helper->update_web_root_path($this->ext_path);
	}

	public $u_action;

	function main()
	{
		// Link to the active (edition) pages
		$str_links = $this->user->lang['INDEX_EDIT_PAGES'];
		$abc_links = '<span id="haut"></span><br /><h2>';
		$params = "/index?mode=index&amp;cap=A";
		$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
		$abc_links .= "$str_links &mdash; <a href =\"$url\">Cliquez ici</a></h2><br><br>";
		
		// Table of links to the passive pages
		$corps = '<h2>' . $this->user->lang['INDEX_DISPLAY_PAGES'] . '</h2>';
		$corps .= "<p>Vous trouverez ci-dessous tout d'abord un tableau des classes et des ordres représentés dans les pages de balises.<br />Cliquez sur les liens pour atteindre la partie de la page d'accueil qui vous intéresse.</p>";

		$corps .= "<table class=\"deg\">";
		foreach ($this->Animalia as $classe)
		{
			$corps .= "\n<tr class=\"deg\"><td class=\"deg1\" colspan=\"4\"><a href=\"#$classe\">$classe</a></td></tr>";
			$nbcases = count ($this->{$classe});
			$nblig = $nbcases >> 2;
			for ($j = 0; $j < $nblig; $j++)
			{
				$corps .= "\n<tr class=\"deg\">";
				$ordre = $this->{$classe}[$j];
				$corps .= "<td class=\"deg0\"><a href=\"#$ordre\">$ordre</a></td>";
				$ordre = $this->{$classe}[($j + $nblig)];
				$corps .= "<td class=\"deg0\"><a href=\"#$ordre\">$ordre</a></td>";
				$ordre = $this->{$classe}[($j + ($nblig * 2))];
				$corps .= "<td class=\"deg0\"><a href=\"#$ordre\">$ordre</a></td>";
				$ordre = $this->{$classe}[($j + ($nblig * 3))];
				$corps .= "<td class=\"deg0\"><a href=\"#$ordre\">$ordre</a></td>";
				$corps .= "</tr>";
			}
		}
		$corps .= "</table>";

		$corps .= "<br><br><p>Vous trouverez ensuite un tableau des ordres avec leurs familles représentées dans les pages de balises.<br />Cliquez sur les liens pour appeler la page d'affichage de ces balises.</p><br>";

		$params = "/index?mode=display";
		$url = append_sid ($this->phpbb_root_path . 'app.' . $this->phpEx . $params);
		$url .= "&amp;fam=";
		foreach ($this->Animalia as $classe)
		{
			$corps .= "<br><table class=\"deg\">";
			$corps .= "\n<tr class=\"deg\"><td class=\"deg3\" id=\"$classe\" colspan=\"4\">$classe</td></tr>";
			$corps .= "</table><br>";
			
			$corps .= "<table class=\"deg\">";
			foreach ($this->{$classe} as $ordre)
			{
				if (!empty ($ordre))
				{
					if (in_array ($ordre, $this->petits_ordres))
						$corps .= "\n<tr class=\"deg\"><td class=\"deg1\" id=\"$ordre\" colspan=\"4\"><a href=\"http://www.insecte.org/forum/app.php/tag/{$ordre}\">$ordre</a></td></tr>";
					else
						$corps .= "\n<tr class=\"deg\"><td class=\"deg1\" id=\"$ordre\" colspan=\"4\">$ordre</td></tr>";
					if (!empty ($this->{$ordre}))
					{
						$nbcases = count ($this->{$ordre});
						$nblig = $nbcases >> 2;
						for ($j = 0; $j < $nblig; $j++)
						{
							$corps .= "\n<tr class=\"deg\">";
							$famille = $this->{$ordre}[$j];
							$corps .= "\n<td class=\"deg0\"><a href=\"$url$famille\">$famille</a></td>";
							$famille = $this->{$ordre}[($j + $nblig)];
							$corps .= "\n<td class=\"deg0\"><a href=\"$url$famille\">$famille</a></td>";
							$famille = $this->{$ordre}[($j + ($nblig *2))];
							$corps .= "\n<td class=\"deg0\"><a href=\"$url$famille\">$famille</a></td>";
							$famille = $this->{$ordre}[($j + ($nblig * 3))];
							$corps .= "\n<td class=\"deg0\"><a href=\"$url$famille\">$famille</a></td>";
							$corps .= "</tr>";
						}
					}
				}
			}
			$corps .= "</table><br>";
		}

		if ($this->auth->acl_get('a_') || $this->auth->acl_getf_global ('m_'))
		{
			$autorisation = 1;
		}
		else
		{
			$autorisation = 0;
		}
		$titre = $this->user->lang['TBALISAGE'];
		page_header($titre);
		$this->template->set_filenames (array(
			'body' => 'index.html',
		));
		$this->template->assign_vars(array(
			'TITLE'		=> $titre,
			'ABC'		=> $abc_links,
			'CORPS'		=> $corps,
			'S_AUTOR'		=> $autorisation,
		));

		page_footer();
	}
}
