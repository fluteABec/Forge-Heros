function resolveImageUrl(imagePath, apiBaseUrl) {
	if (!imagePath) return null
	if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
		return imagePath
	}

	const cleanBaseUrl = apiBaseUrl.replace(/\/$/, '')
	const cleanPath = imagePath.startsWith('/') ? imagePath : `/${imagePath}`

	return `${cleanBaseUrl}${cleanPath}`
}

function CharacterCard({ character, apiBaseUrl, onSelect }) {
	const className =
		character.characterClass?.name || character.class?.name || 'Classe inconnue'
	const raceName = character.race?.name || 'Race inconnue'
	const rawImage = character.image || character.imageUrl || character.avatar || null
	const imageUrl = resolveImageUrl(rawImage, apiBaseUrl)
	const levelLabel = character.level ?? '-'

	return (
		<article
			className="card character-card character-card-clickable"
			aria-label={`Character ${character.name}`}
			onClick={() => onSelect(character.id)}
			onKeyDown={(event) => {
				if (event.key === 'Enter' || event.key === ' ') {
					event.preventDefault()
					onSelect(character.id)
				}
			}}
			role="button"
			tabIndex={0}
		>
			{imageUrl ? (
				<img
					className="character-card-image"
					src={imageUrl}
					alt={`Avatar de ${character.name}`}
				/>
			) : (
				<div className="character-card-placeholder">Aucun avatar</div>
			)}

			<div className="card-body character-card-body">
				<div className="character-card-header">
					<div>
						<h3 className="character-card-title">{character.name}</h3>
						<p className="stat-label">
							{className} - {raceName}
						</p>
						<p className="text-link" style={{ marginTop: '0.5rem' }}>
							Voir le detail
						</p>
					</div>

					<span className="character-hp-badge">Niv. {levelLabel}</span>
				</div>
			</div>
		</article>
	)
}

export default CharacterCard
