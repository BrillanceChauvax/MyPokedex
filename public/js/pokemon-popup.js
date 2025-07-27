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
    
    showLoadingIndicator();
    
    try {
        const response = await fetch(`/api/pokemon/${pokemonId}`);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const pokemon = await response.json();
        displayPokemonPopup(pokemon);
    } catch (error) {
        hideLoadingIndicator();
        showErrorMessage('Erreur lors du chargement des données du Pokémon');
    }
}

function showLoadingIndicator() {
    const overlay = document.getElementById('pokemon-popup-overlay');
    if (overlay) {
        overlay.style.display = 'flex';
        overlay.innerHTML = `
            <div class="popup-container">
                <button class="popup-close" onclick="closePokemonPopup()">&times;</button>
                <div class="popup-content">
                    <div class="popup-body">
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; text-align: center;">
                            <div style="font-size: 2em; margin-bottom: 15px;">⚡</div>
                            <div style="font-size: 1.2em; color: #333;">Chargement des données...</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.style.overflow = 'hidden';
    }
}

function hideLoadingIndicator() {
    const overlay = document.getElementById('pokemon-popup-overlay');
    if (overlay) {
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function showErrorMessage(message) {
    alert(message);
}

function displayPokemonPopup(pokemon) {
    restorePopupContent();
    
    document.getElementById('popup-pokemon-image').src = pokemon.image_url || '/images/pokemon-placeholder.png';
    document.getElementById('popup-pokemon-image').alt = pokemon.nom;
    document.getElementById('popup-pokemon-name').textContent = pokemon.nom;
    document.getElementById('popup-pokemon-generation').textContent = pokemon.generation;
    document.getElementById('popup-pokemon-number').textContent = pokemon.numero_national.toString().padStart(3, '0');

    displayPokemonTypes(pokemon.types);

    if (pokemon.evolution_chain && pokemon.evolution_chain.length > 1) {
        displayEvolutionChain(pokemon.evolution_chain);
        document.getElementById('evolution-section').style.display = 'block';
    } else {
        document.getElementById('evolution-section').style.display = 'none';
    }

    document.getElementById('pokemon-popup-overlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function restorePopupContent() {
    const overlay = document.getElementById('pokemon-popup-overlay');
    if (overlay) {
        const originalContent = `
            <div class="popup-container">
                <div class="popup-content">
                    <button class="popup-close" onclick="closePokemonPopup()">&times;</button>
                    
                    <div class="popup-body">
                        <div class="pokemon-main-info">
                            <div class="pokemon-image">
                                <img id="popup-pokemon-image" src="" alt="Pokémon" />
                            </div>
                            
                            <div class="pokemon-details">
                                <h2 id="popup-pokemon-name"></h2>
                                <div class="pokemon-meta">
                                    <span class="generation">Génération <span id="popup-pokemon-generation"></span></span>
                                    <span class="pokedex-number">#<span id="popup-pokemon-number"></span></span>
                                </div>
                                
                                <div class="pokemon-types" id="popup-pokemon-types">
                                    <!-- Types seront ajoutés dynamiquement -->
                                </div>
                            </div>
                        </div>

                        <div class="evolution-section" id="evolution-section" style="display: none;">
                            <h3>Chaîne d'évolution</h3>
                            <div class="evolution-chain" id="evolution-chain">
                                <!-- Chaîne d'évolution sera ajoutée dynamiquement -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        overlay.innerHTML = originalContent;
    }
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

function closePokemonPopup() {
    const overlay = document.getElementById('pokemon-popup-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
    document.body.style.overflow = 'auto';
}

window.closePokemonPopup = closePokemonPopup;
