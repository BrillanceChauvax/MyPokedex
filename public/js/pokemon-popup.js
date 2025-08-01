document.addEventListener('DOMContentLoaded', function() {
    
    const pokemonRows = document.querySelectorAll('tr[data-pokemon-id]');
    
    pokemonRows.forEach(row => {
        const pokemonId = row.getAttribute('data-pokemon-id');
        
        row.style.cursor = 'pointer';
        row.style.transition = 'all 0.2s ease';
        
        row.addEventListener('click', function() {
            openPokemonPopup(pokemonId);
        });
        
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f0f8ff';
            this.style.transform = 'translateY(-1px)';
            this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePokemonPopup();
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('popup-overlay')) {
            closePokemonPopup();
        }
    });
});

async function openPokemonPopup(pokemonId) {
    const overlay = document.getElementById('pokemon-popup-overlay');
    overlay.style.display = 'flex';  // Affiche la popup

    try {
        const response = await fetch(`/api/pokemon/${pokemonId}`);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        const pokemon = await response.json();

        displayPokemonPopup(pokemon);
    } catch (error) {
        showErrorMessage('Erreur lors du chargement des données du Pokémon');
    }
}

function showErrorMessage(message) {
    alert(message);
}

function displayPokemonPopup(pokemon) {
    
    // Affichage conditionnel de la génération
    const generationElement = document.getElementById('popup-pokemon-generation');
    if (pokemon.generation === 99) {
        generationElement.textContent = 'GIGAMAX';
    } else if (pokemon.generation === 98) {
        generationElement.textContent = 'MÉGA-ÉVOLUTION';
    } else {
        generationElement.textContent = pokemon.generation;
    }

    const pokedexNumberElement = document.querySelector('.pokedex-number');
    if (pokemon.generation === 98 || pokemon.generation === 99) {
        pokedexNumberElement.style.display = 'none';
    } else {
        pokedexNumberElement.style.display = 'inline';
    }

    document.getElementById('popup-pokemon-image').src = pokemon.image_url || '/images/pokemon-placeholder.png';
    document.getElementById('popup-pokemon-image').alt = pokemon.nom;
    document.getElementById('popup-pokemon-name').textContent = pokemon.nom;
    document.getElementById('popup-pokemon-number').textContent = pokemon.numero_national.toString().padStart(3, '0');
    document.getElementById('popup-pokemon-height').textContent = pokemon.taille ? pokemon.taille.toFixed(2) + ' m' : 'N/A';
    document.getElementById('popup-pokemon-weight').textContent = pokemon.poids ? pokemon.poids.toFixed(1) + ' kg' : 'N/A';
    document.getElementById('popup-pokemon-description').textContent = pokemon.description || 'Aucune description disponible.';

    // Cri audio
    if (pokemon.cri_url) {
    document.getElementById('popup-pokemon-cry').innerHTML = `<audio controls src="${pokemon.cri_url}">Votre navigateur ne supporte pas la lecture audio.</audio>`;
    } else {
    document.getElementById('popup-pokemon-cry').innerHTML = '<em>Audio du cri non disponible.</em>';
    }

    // Stats tableau graphique
    if (pokemon.stats) {
    const maxStatValue = 255;  // Valeur max possible 
    const statsMap = [
        { label: 'PV', value: pokemon.stats.pv, barClass: 'stat-bar-fill-pv' },
        { label: 'Attaque', value: pokemon.stats.attaque, barClass: 'stat-bar-fill-attaque' },
        { label: 'Attaque Spé.', value: pokemon.stats.attaque_spe, barClass: 'stat-bar-fill-attaquespe' },
        { label: 'Défense', value: pokemon.stats.defense, barClass: 'stat-bar-fill-defense' },
        { label: 'Défense Spé.', value: pokemon.stats.defense_spe, barClass: 'stat-bar-fill-defensespe' },
        { label: 'Vitesse', value: pokemon.stats.vitesse, barClass: 'stat-bar-fill-vitesse' },
    ];

    let htmlStats = `<table class="stats-table"><tbody>`;

    statsMap.forEach(stat => {
        // Pourcentage de remplissage (protégé à 100%)
        const widthPercent = Math.min(100, (stat.value / maxStatValue) * 100);
        htmlStats += `
        <tr>
            <th>${stat.label}</th>
            <td>
            <div class="stat-bar-container">
                <div class="stat-bar-fill ${stat.barClass}" style="width: ${widthPercent}%"></div>
            </div>
            </td>
            <td class="stat-value">${stat.value}</td>
        </tr>
        `;
    });

    htmlStats += `</tbody></table>`;
    document.getElementById('popup-pokemon-stats').innerHTML = htmlStats;
    } else {
    document.getElementById('popup-pokemon-stats').innerHTML = '<p>Statistiques non disponibles.</p>';
    }

    displayPokemonTypes(pokemon.types);
    displayPokemonTalents(pokemon.talents);

    if (pokemon.evolution_chain && pokemon.evolution_chain.length > 1) {
        displayEvolutionChain(pokemon.evolution_chain);
        document.getElementById('evolution-section').style.display = 'block';
    } else {
        document.getElementById('evolution-section').style.display = 'none';
    }

    document.getElementById('pokemon-popup-overlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function displayPokemonTypes(types) {
    const typesContainer = document.getElementById('popup-pokemon-types');
    if (!typesContainer) return;
        
    typesContainer.innerHTML = '';

    types.forEach(type => {
        const typeBadge = createTypeBadge(type);
        typesContainer.appendChild(typeBadge);
    });
}

function createTypeBadge(type) {
    const badge = document.createElement('div');
    badge.className = `type-badge type-${type.nom.toLowerCase().replace(/[éè]/g, 'e')}`;
    badge.title = type.nom;
        
    badge.innerHTML = `
        <img src="${type.icone_url}" alt="${type.nom}" class="type-icon" 
            onerror="this.style.display='none'">
    `;
        
    return badge;
}

function displayEvolutionChain(evolutionChain) {
    const chainContainer = document.getElementById('evolution-chain');
    if (!chainContainer) return;
    
    chainContainer.innerHTML = '';

    evolutionChain.forEach((pokemon, index) => {
        const pokemonElement = document.createElement('div');
        pokemonElement.className = 'evolution-pokemon';
        pokemonElement.setAttribute('data-pokemon-id', pokemon.id);
        pokemonElement.onclick = () => openPokemonPopup(pokemon.id);

        const typesHtml = pokemon.types.map(type => 
            `<img src="${type.icone_url}" alt="${type.nom}" class="type-icon" 
                  onerror="this.style.display='none'" title="${type.nom}">`
        ).join('');

        pokemonElement.innerHTML = `
            <img src="${pokemon.image_url}" alt="${pokemon.nom}" 
                 onerror="this.src='/images/pokemon-placeholder.png'">
            <div class="evolution-pokemon-name">${pokemon.nom}</div>
            <div class="evolution-pokemon-number">#${pokemon.numero_national.toString().padStart(3, '0')}</div>
            <div class="evolution-pokemon-types">${typesHtml}</div>
        `;

        chainContainer.appendChild(pokemonElement);

        if (index < evolutionChain.length - 1) {
            const arrowContainer = document.createElement('div');
            arrowContainer.className = 'evolution-arrow-container';
            
            const nextPokemon = evolutionChain[index + 1];
            const condition = nextPokemon.condition || '';
            
            arrowContainer.innerHTML = `
                <div class="evolution-arrow">↓</div>
                <div class="evolution-condition">${condition}</div>
            `;
            
            chainContainer.appendChild(arrowContainer);
        }
    });
}

function displayPokemonTalents(talents) {
  const container = document.getElementById('popup-pokemon-talents');
  if (!container) return;
  
  container.innerHTML = ''; 
  
  if (!talents || talents.length === 0) {
    container.textContent = 'Aucun talent disponible.';
    return;
  }
  
  talents.forEach(talent => {
    const badge = document.createElement('div');
    badge.classList.add('talent-badge');

    // Texte affiché : nom + indicatif talent caché
    badge.textContent = talent.nom + (talent.is_hidden ? ' (Talent caché)' : '');

    // Créer un élément tooltip caché
    const tooltip = document.createElement('div');
    tooltip.classList.add('tooltip');
    tooltip.textContent = talent.description || 'Pas de description.';

    badge.appendChild(tooltip);

    // Gestion clic : bascule l’affichage de la tooltip
    badge.addEventListener('click', (e) => {
      e.stopPropagation();
      
      // Fermer toutes les autres tooltips ouvertes
      document.querySelectorAll('.talent-badge.show-tooltip').forEach(el => {
        if (el !== badge) el.classList.remove('show-tooltip');
      });

      badge.classList.toggle('show-tooltip');
    });

    // Fermer tooltip si clic ailleurs dans la popup
    document.getElementById('pokemon-popup-overlay').addEventListener('click', () => {
      badge.classList.remove('show-tooltip');
    });
    
    container.appendChild(badge);
  });
}

function closePokemonPopup() {
    const overlay = document.getElementById('pokemon-popup-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
    document.body.style.overflow = 'auto';
}

window.closePokemonPopup = closePokemonPopup;
