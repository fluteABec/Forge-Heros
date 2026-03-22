import { useEffect, useState } from 'react'
import CharacterCard from './CharacterCard'
import CharacterDetail from './CharacterDetail'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

function normalizeCharacters(payload) {
	// Accepte plusieurs formats de reponse API pour rester robuste.
	if (Array.isArray(payload)) return payload
	if (Array.isArray(payload?.items)) return payload.items
	if (Array.isArray(payload?.data)) return payload.data
	return []
}

function getClassName(character) {
	return character.characterClass?.name || character.class?.name || 'Classe inconnue'
}

function getRaceName(character) {
	return character.race?.name || 'Race inconnue'
}

function getLevel(character) {
	return Number(character.level ?? 0)
}

function CharactersPage({ onNavigateToGroups, externalSelectedCharacterId }) {
	const [characters, setCharacters] = useState([])
	const [isLoading, setIsLoading] = useState(true)
	const [error, setError] = useState('')
	const [nameFilter, setNameFilter] = useState('')
	const [classFilter, setClassFilter] = useState('')
	const [raceFilter, setRaceFilter] = useState('')
	const [sortBy, setSortBy] = useState('name-asc')
	const [selectedCharacterId, setSelectedCharacterId] = useState(null)

	useEffect(() => {
		if (externalSelectedCharacterId !== undefined) {
			setSelectedCharacterId(externalSelectedCharacterId)
		}
	}, [externalSelectedCharacterId])

	useEffect(() => {
		const controller = new AbortController()

		async function loadCharacters() {
			setIsLoading(true)
			setError('')

			try {
				// Recupere la liste publique depuis Symfony API.
				const response = await fetch(`${API_BASE_URL}/api/v1/characters`, {
					signal: controller.signal,
				})

				if (!response.ok) {
					throw new Error(`HTTP ${response.status}`)
				}

				const json = await response.json()
				setCharacters(normalizeCharacters(json))
			} catch (err) {
				if (err.name !== 'AbortError') {
					setError('Impossible de charger les personnages pour le moment.')
				}
			} finally {
				setIsLoading(false)
			}
		}

		loadCharacters()

		return () => controller.abort()
	}, [])

	const classOptions = [...new Set(characters.map((character) => getClassName(character)))]

	const raceOptions = [...new Set(characters.map((character) => getRaceName(character)))]

	const normalizedNameFilter = nameFilter.trim().toLowerCase()

	// Filtre local: nom + classe + race.
	const filteredCharacters = characters.filter((character) => {
		const characterName = (character.name || '').toLowerCase()
		const characterClassName = getClassName(character)
		const characterRaceName = getRaceName(character)

		const matchesName =
			normalizedNameFilter === '' || characterName.includes(normalizedNameFilter)
		const matchesClass = classFilter === '' || characterClassName === classFilter
		const matchesRace = raceFilter === '' || characterRaceName === raceFilter

		return matchesName && matchesClass && matchesRace
	})

	// Tri local configurable pour la demo front.
	const sortedCharacters = [...filteredCharacters].sort((a, b) => {
		if (sortBy === 'name-asc') {
			return (a.name || '').localeCompare(b.name || '', 'fr')
		}

		if (sortBy === 'name-desc') {
			return (b.name || '').localeCompare(a.name || '', 'fr')
		}

		if (sortBy === 'level-asc') {
			return getLevel(a) - getLevel(b)
		}

		if (sortBy === 'level-desc') {
			return getLevel(b) - getLevel(a)
		}

		return 0
	})

	if (selectedCharacterId !== null) {
		// En mode detail, la liste est remplacee par la vue detail du personnage.
		return (
			<CharacterDetail
				characterId={selectedCharacterId}
				apiBaseUrl={API_BASE_URL}
				onBack={() => setSelectedCharacterId(null)}
				onNavigateToGroups={onNavigateToGroups}
			/>
		)
	}

	return (
		<section className="panel home-section" id="characters">
			<p className="eyebrow">Personnages</p>
			<h2>Liste des personnages</h2>

			<div className="filters" aria-label="Filtres des personnages">
				<label>
					Nom
					<input
						type="text"
						placeholder="Rechercher par nom"
						value={nameFilter}
						onChange={(event) => setNameFilter(event.target.value)}
					/>
				</label>

				<label>
					Classe
					<select
						value={classFilter}
						onChange={(event) => setClassFilter(event.target.value)}
					>
						<option value="">Toutes les classes</option>
						{classOptions.map((option) => (
							<option key={option} value={option}>
								{option}
							</option>
						))}
					</select>
				</label>

				<label>
					Race
					<select
						value={raceFilter}
						onChange={(event) => setRaceFilter(event.target.value)}
					>
						<option value="">Toutes les races</option>
						{raceOptions.map((option) => (
							<option key={option} value={option}>
								{option}
							</option>
						))}
					</select>
				</label>

				<label>
					Tri
					<select value={sortBy} onChange={(event) => setSortBy(event.target.value)}>
						<option value="name-asc">Nom (A-Z)</option>
						<option value="name-desc">Nom (Z-A)</option>
						<option value="level-asc">Niveau (croissant)</option>
						<option value="level-desc">Niveau (decroissant)</option>
					</select>
				</label>
			</div>

			{isLoading && <p className="section-text">Chargement des personnages...</p>}

			{error && !isLoading && <p className="is-invalid">{error}</p>}

			{!isLoading && !error && characters.length === 0 && (
				<p className="section-text">Aucun personnage a afficher.</p>
			)}

			{!isLoading && !error && characters.length > 0 && filteredCharacters.length === 0 && (
				<p className="section-text">Aucun personnage ne correspond aux filtres.</p>
			)}

			{!isLoading && !error && sortedCharacters.length > 0 && (
				<div className="character-card-grid">
					{sortedCharacters.map((character) => (
						<CharacterCard
							key={character.id}
							character={character}
							apiBaseUrl={API_BASE_URL}
							onSelect={setSelectedCharacterId}
						/>
					))}
				</div>
			)}
		</section>
	)
}

export default CharactersPage
