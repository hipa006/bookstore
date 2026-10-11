"""Books Store chatbot - Flask backend.

Run:  python app.py
Needs: pip install flask flask-cors mysql-connector-python
"""
import re
import unicodedata

import mysql.connector
from flask import Flask, jsonify, request
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# ---------------------------------------------------------------------------
# Configuration (adapt to your project)
# ---------------------------------------------------------------------------
DB_CONFIG = {"host": "localhost", "user": "root", "password": "", "database": "books_db"}
TABLE = "produit"
COL_TITLE = "titre"
COL_AUTHOR = "auteur"
COL_CATEGORY = "categorie"  
MAX_RESULTS = 5

# Pages of the website: key -> (button label, url relative to index.php)
LINKS = {
    "livres": ("Voir les livres", "livres.php"),  # <- adapt to the real name of your books page
    "panier": ("Ouvrir le panier", "Panier.php"),
    "contact": ("Page Contact", "Contact.php"),
    "about": ("Page À propos", "About.php"),
    "login": ("Se connecter", "login.php"),
}

SUGGESTIONS = [
    "Voir le catalogue",
    "Comment commander ?",
    "Code de retrait",
    "Moyens de paiement",
    "Contact",
]

# ---------------------------------------------------------------------------
# Website knowledge: (intent name, keywords, answer, link keys)
# Keywords are written WITHOUT accents, in French / English / Darija (latin).
# Words of 4+ letters match as prefixes ("command" matches "commander").
# To teach the bot something new, just add a line here.
# The order matters: on a tie, the first intent wins.
# ---------------------------------------------------------------------------
INTENTS = [
    ("thanks", ["merci", "thanks", "thank you", "shukran", "chokran", "choukran", "barak allah"],
     "Avec plaisir ! N'hésitez pas si vous avez d'autres questions.", []),

    ("bye", ["au revoir", "bye", "a bientot", "a plus", "bslama", "besslama", "ciao"],
     "Au revoir et à bientôt chez Books Store !", []),

    ("how_are_you", ["ca va", "labas", "kif dayr", "kifach nta", "how are you", "comment vas", "comment allez"],
     "Très bien, merci ! Et vous ? Dites-moi ce que vous cherchez.", []),

    ("identity", ["qui es", "tu es qui", "t es qui", "who are you", "ton nom", "chatbot", "bot", "chkoun nta", "smiytk"],
     "Je suis l'assistant virtuel de Books Store. Je peux chercher un livre pour vous et répondre "
     "à vos questions sur la commande, le retrait en magasin, le paiement, le panier ou votre compte.", []),

    ("pickup_code", ["code de retrait", "code retrait", "code", "mon code"],
     "Après la validation de votre commande, vous recevez un code de retrait (affiché sur la page de "
     "confirmation). Conservez-le : il vous sera demandé en magasin pour récupérer vos livres. "
     "Code perdu ? Contactez-nous via la page Contact.", ["contact"]),

    ("payment", ["paie", "payer", "payez", "paye", "pay", "cash", "espece", "carte bancaire",
                 "mode de paiement", "khlas", "khelles", "nkhelles", "dfa3"],
     "Le paiement se fait sur place, en magasin, au moment du retrait de vos livres : "
     "vous ne payez rien en ligne.", []),

    ("delivery", ["livraison", "livrer", "livrez", "livrais", "domicile", "delivery", "ship", "expedition", "frais de port", "tawsil", "twsil"],
     "Chez Books Store, le principe est « Commandez en ligne, retirez en magasin » : les commandes se "
     "retirent en magasin. Pour toute question sur une livraison, écrivez-nous via la page Contact.", ["contact"]),

    ("cart", ["panier", "cart", "basket", "ajouter", "supprimer", "vider", "quantite", "zid"],
     "Ajoutez vos livres au panier depuis la page Livres, puis ouvrez la page Panier pour vérifier vos "
     "articles et valider votre commande.", ["livres", "panier"]),

    ("pickup", ["retrait", "retirer", "retirez", "recuperer", "magasin", "pickup", "pick up", "withdraw"],
     "Vous retirez vos livres directement en magasin : une fois la commande validée, présentez votre "
     "code de retrait et payez sur place. Pour l'adresse et les horaires, consultez la page Contact.", ["contact"]),

    ("order", ["command", "acheter", "achat", "passer une", "order", "buy", "nchri", "bghit nchri"],
     "Commander est simple :\n"
     "1. Choisissez vos livres sur la page Livres et ajoutez-les au panier.\n"
     "2. Vérifiez puis validez votre commande depuis le Panier.\n"
     "3. Vous recevez votre code de retrait.\n"
     "4. Passez en magasin avec ce code et payez sur place.", ["livres", "panier"]),

    ("account", ["compte", "connexion", "connecter", "login", "log in", "sign in", "inscri", "register",
                 "mot de passe", "password", "identifiant"],
     "Pour créer un compte ou vous connecter, utilisez la page de connexion. Une fois connecté, votre "
     "nom s'affiche en haut du site. En cas de problème d'accès, écrivez-nous via la page Contact.",
     ["login", "contact"]),

    ("logout", ["deconnexion", "deconnecter", "logout", "log out", "sign out"],
     "Cliquez sur « Déconnexion » dans le menu, en haut à droite du site.", []),

    ("contact", ["contact", "joindre", "telephone", "tel", "numero", "phone", "appeler", "adresse", "address",
                 "horaire", "heure", "ouvert", "ferme", "ouverture", "hours", "localisation", "ou se trouve",
                 "reclamation", "probleme", "support", "assistance", "service client", "responsable"],
     "Pour l'adresse du magasin, les horaires ou pour nous joindre, rendez-vous sur la page Contact.",
     ["contact"]),

    ("about", ["a propos", "about", "qui etes", "qui sommes", "presentation", "presentez", "equipe",
               "librairie", "books store"],
     "Books Store est une librairie avec commande en ligne et retrait en magasin : vous choisissez vos "
     "livres sur le site, puis vous les récupérez et les payez sur place. "
     "Plus d'informations sur la page À propos.", ["about"]),

    ("help", ["aide", "help", "que peux", "peux tu", "pouvez vous", "quoi faire", "que sais", "options",
              "menu", "3awni", "sa3dni"],
     "Je peux :\n"
     "• chercher un livre par titre ou par auteur\n"
     "• afficher le catalogue et les prix\n"
     "• expliquer comment commander, retirer et payer\n"
     "• vous aider avec le panier, votre compte et le contact\n"
     "Posez-moi votre question !", []),

    ("price", ["prix", "tarif", "combien", "cout", "price", "chhal", "taman", "thaman"],
     "Le prix de chaque livre est indiqué en DH sur la page Livres. Donnez-moi un titre ou un auteur "
     "et je vous donne son prix.", ["livres"]),

    ("catalog", ["catalogue", "livre", "book", "ktab", "kitab", "ktoub", "kotob", "nouveaute", "collection",
                 "tous", "disponible", "stock"],
     None, ["livres"]),

    ("find_book", ["cherche", "chercher", "trouver", "recherche", "auteur", "titre", "ecrit par", "roman",
                   "bghit", "search", "find", "looking", "recommande", "conseille", "suggest", "categorie", "genre", "rayon", "theme", "sujet",],
     "Bien sûr ! Donnez-moi le titre ou le nom de l'auteur (par exemple « Camus »).", []),
]

INTENT_BY_NAME = {name: (answer, links) for name, _, answer, links in INTENTS}
SEARCH_INTENTS = {"price", "catalog", "find_book"}  # these look in the database when a title/author is given

# ---------------------------------------------------------------------------
# Language helpers
# ---------------------------------------------------------------------------
GREETING_WORDS = {"bonjour", "bonsoir", "salut", "salam", "slm", "hello", "hi", "hey", "coucou",
                  "ahlan", "marhba", "salamo", "assalam"}

FILLER = {"monsieur", "madame", "mr", "mme", "svp", "stp", "please", "plz", "sidi", "lalla", "khouya",
          "khoya", "alaykom", "alaykum", "alaykoum", "alikoum", "aleykoum", "3alaykom", "bonne",
          "journee", "soiree"}

STOPWORDS = set("""
le la les l un une des de du d et ou a au aux en dans sur pour par avec sans ce cet cette ces mon ma mes
ton ta tes son sa ses je tu il elle nous vous ils elles me te se y est es suis sont etes ai as avez avons
ont que qui quoi quel quelle quels quelles ne pas plus s c j m t n qu si comme mais donc car aussi bien
tres moins voir montre montrez afficher donne donnez liste veux voudrais aimerais souhaite besoin savoir
dire dites pouvez peux peut puis pourrais pourriez faire fais comment pourquoi quand existe avoir
the an of for to in on and is are do you have i want with my show me
li f fi dyal dial mn 3la wach wash kayn kayna ila chi hada hadi had hadchi ana nta ghir bzaf mzyan
""".split())


def normalize(text):
    """Lowercase, remove accents and punctuation."""
    text = unicodedata.normalize("NFD", text.lower())
    text = "".join(c for c in text if unicodedata.category(c) != "Mn")
    text = re.sub(r"[^a-z0-9\s]", " ", text)
    return re.sub(r"\s+", " ", text).strip()


def kw_match(kw, text):
    """Keyword match at a word start; words of 3 letters or less must match exactly."""
    pattern = r"\b" + re.escape(kw) + (r"\b" if len(kw) <= 3 else "")
    return re.search(pattern, text) is not None


_SINGLE_KWS = {kw for _, kws, _, _ in INTENTS for kw in kws if " " not in kw}
_INTENT_WORDS = {w for _, kws, _, _ in INTENTS for kw in kws for w in kw.split()}


def is_intent_word(word):
    """True for words that belong to the website vocabulary (so they are not used as a book search)."""
    if word in _INTENT_WORDS:
        return True
    return any(len(kw) > 3 and word.startswith(kw) for kw in _SINGLE_KWS)


def detect_intent(text):
    best, best_score = None, 0
    for name, kws, _, _ in INTENTS:
        score = sum(len(kw.split()) for kw in kws if kw_match(kw, text))
        if score > best_score:  # strict: on a tie, the first intent in the list wins
            best, best_score = name, score
    return best


def greeting_opener(greet_words):
    first = greet_words[0]
    if first == "bonsoir":
        return "Bonsoir"
    if first in {"salam", "slm", "ahlan", "marhba", "salamo", "assalam"}:
        return "Salam"
    if first in {"hello", "hi", "hey"}:
        return "Hello"
    return "Bonjour"


# ---------------------------------------------------------------------------
# Database
# ---------------------------------------------------------------------------
def run_query(sql, params=()):
    conn = None
    cursor = None
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        cursor.execute(sql, params)
        return cursor.fetchall()
    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()


def fmt_price(value):
    try:
        number = float(value)
    except (TypeError, ValueError):
        return ""
    return f"{int(number)} DH" if number == int(number) else f"{number:.2f} DH"


def row_to_book(row):
    return {
        "title": row.get(COL_TITLE) or row.get("title") or "Livre",
        "author": row.get(COL_AUTHOR) or row.get("author") or "",
        "category": row.get(COL_CATEGORY) or "",
        "price": fmt_price(row.get("prix", row.get("price"))),
    }


def stem(word):
    # "psychologique" -> "psycho" so it also matches "psychologie"
    return word[:6] if len(word) >= 8 else word


def search_books(keywords):
    keywords = [stem(w) for w in keywords[:6]]
    cols = [COL_TITLE, COL_AUTHOR, COL_CATEGORY]

    like_params = []
    for word in keywords:
        like_params += [f"%{word}%"] * len(cols)

    one = " OR ".join(f"{c} LIKE %s" for c in cols)
    score = " + ".join(f"({c} LIKE %s)" for c in cols)
    where = " OR ".join([one] * len(keywords))
    order = " + ".join([score] * len(keywords))

    sql = f"SELECT * FROM {TABLE} WHERE {where} ORDER BY ({order}) DESC LIMIT {MAX_RESULTS}"
    return [row_to_book(r) for r in run_query(sql, like_params * 2)]

def catalog_books():
    return [row_to_book(r) for r in run_query(f"SELECT * FROM {TABLE} LIMIT {MAX_RESULTS}")]


# ---------------------------------------------------------------------------
# Reply building
# ---------------------------------------------------------------------------
def make(text, links=(), books=(), suggestions=False):
    return {
        "response": text,
        "links": [{"label": LINKS[k][0], "url": LINKS[k][1]} for k in links],
        "books": list(books),
        "suggestions": SUGGESTIONS if suggestions else [],
    }


DB_ERROR = ("Je n'arrive pas à consulter le catalogue pour le moment. "
            "Réessayez dans un instant ou parcourez la page Livres.")

FALLBACK = ("Je n'ai pas bien compris, mais je peux vous aider avec la recherche de livres, la commande, "
            "le retrait en magasin, le paiement, le panier, votre compte ou le contact. "
            "Essayez par exemple un titre ou un auteur.")


def answer(intent, keywords):
    try:
        if intent in SEARCH_INTENTS and keywords:
            return reply_search(keywords)
        if intent == "catalog":
            books = catalog_books()
            if not books:
                return make("Le catalogue est vide pour le moment.")
            return make("Voici quelques livres de notre catalogue :", ["livres"], books)
        if intent:
            text, links = INTENT_BY_NAME[intent]
            return make(text, links, suggestions=(intent == "help"))
        if keywords:
            return reply_search(keywords)
    except Exception as exc:  # database down, wrong column name...
        print("Database error:", exc)
        return make(DB_ERROR, ["livres"])
    return make(FALLBACK, suggestions=True)


def reply_search(keywords):
    books = search_books(keywords)
    if books:
        return make("Voici ce que j'ai trouvé :", ["livres"], books)
    return make(
        f"Je n'ai trouvé aucun livre pour « {' '.join(keywords)} ». Essayez avec le titre complet ou le "
        "nom de l'auteur, ou parcourez la page Livres.",
        ["livres"],
    )


def build_reply(message):
    text = normalize(message)
    words = text.split()

    greet_words = [w for w in words if w in GREETING_WORDS]
    core = [w for w in words if w not in GREETING_WORDS | FILLER | STOPWORDS]

    if greet_words:
        opener = greeting_opener(greet_words)
        if not core:
            return make(
                f"{opener} ! Bienvenue chez Books Store. Je peux vous aider à trouver un livre ou répondre "
                "à vos questions sur le site (commande, retrait, paiement, compte...).",
                suggestions=True,
            )

    intent = detect_intent(text)
    keywords = [w for w in core if len(w) >= 3 and not is_intent_word(w)]
    result = answer(intent, keywords)

    if greet_words:
        result["response"] = f"{opener} ! {result['response']}"
    return result


# ---------------------------------------------------------------------------
# Routes
# ---------------------------------------------------------------------------
@app.route("/")
def home():
    return "Server is running!"


@app.route("/chat", methods=["POST"])
def chat():
    data = request.get_json(silent=True) or {}
    message = str(data.get("message", "")).strip()[:300]
    if not message:
        return jsonify(make("Écrivez-moi un message pour commencer.")), 400
    return jsonify(build_reply(message))


if __name__ == "__main__":
    app.run(debug=True, port=5000)
