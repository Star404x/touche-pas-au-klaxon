# MCD - Modèle Conceptuel de Données
## TOUCHE PAS AU KLAXON

### Entités et Relations

```
┌─────────────────────┐
│   UTILISATEURS      │
├─────────────────────┤
│ PK: id              │
│    nom              │
│    prenom           │
│    email (UNIQUE)   │
│    telephone        │
│    mot_de_passe     │
│    role             │
│    created_at       │
│    updated_at       │
└──────────┬──────────┘
           │
           │ 1:N
           │
           ▼
┌─────────────────────┐
│      TRAJETS        │
├─────────────────────┤
│ PK: id              │
│ FK: utilisateur_id  │
│ FK: agence_depart_id│
│ FK: agence_arrivee_id
│    date_heure_depart
│    date_heure_arrivee
│    places_totales   │
│    places_dispon    │
│    created_at       │
│    updated_at       │
└──────────┬──────────┘
           │
           │ N:1
           │
    ┌──────┴──────┐
    │             │
    ▼             ▼
┌─────────────┐ ┌─────────────┐
│   AGENCES   │ │   AGENCES   │
├─────────────┤ ├─────────────┤
│ PK: id      │ │ PK: id      │
│    nom      │ │    nom      │
│ (DEPART)    │ │ (ARRIVÉE)   │
└─────────────┘ └─────────────┘
```

### Cardinalités

| Relation | Type | Description |
|----------|------|-------------|
| UTILISATEURS → TRAJETS | 1:N | Un utilisateur peut créer plusieurs trajets |
| TRAJETS → AGENCES (départ) | N:1 | Un trajet a une agence de départ |
| TRAJETS → AGENCES (arrivée) | N:1 | Un trajet a une agence d'arrivée |

### Attributs Clés

**UTILISATEURS**
- id: Identifiant unique (PK)
- nom: Chaîne de caractères
- prenom: Chaîne de caractères
- email: Unique, format email
- telephone: Format E.164
- mot_de_passe: Hash bcrypt
- role: ENUM(USER, ADMIN)
- created_at, updated_at: Timestamps

**AGENCES**
- id: Identifiant unique (PK)
- nom: Unique, nom de ville

**TRAJETS**
- id: Identifiant unique (PK)
- utilisateur_id: FK → UTILISATEURS(id)
- agence_depart_id: FK → AGENCES(id)
- agence_arrivee_id: FK → AGENCES(id)
- date_heure_depart: DATETIME, > NOW()
- date_heure_arrivee: DATETIME, > date_heure_depart
- places_totales: INT > 0
- places_disponibles: INT, <= places_totales
- created_at, updated_at: Timestamps

### Contraintes

1. **Intégrité Référentielle**
   - FK utilisateur_id CASCADE DELETE
   - FK agence_depart_id RESTRICT
   - FK agence_arrivee_id RESTRICT

2. **Domaine**
   - email: Format valide
   - telephone: E.164
   - places: places_disponibles <= places_totales
   - dates: depart < arrivee
   - agences: depart ≠ arrivee

3. **Unicité**
   - UTILISATEURS.email
   - AGENCES.nom

### Normalisé 3NF ✅

Toutes les tables respectent la 3ème forme normale (3NF):
- ✅ 1NF: Attributs atomiques
- ✅ 2NF: Dépendances fonctionnelles complètes
- ✅ 3NF: Aucune dépendance transitive
