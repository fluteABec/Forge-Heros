function getRemainingPlaces(group) {
  const memberCount = Number(group.memberCount ?? 0)
  const maxSize = Number(group.maxSize ?? 0)
  return Math.max(0, maxSize - memberCount)
}

function GroupCard({ group, onSelect }) {
  const memberCount = Number(group.memberCount ?? 0)
  const maxSize = Number(group.maxSize ?? 0)
  const remainingPlaces = getRemainingPlaces(group)

  return (
    <article
      className="card character-card character-card-clickable"
      role="button"
      tabIndex={0}
      onClick={() => onSelect(group.id)}
      onKeyDown={(event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault()
          onSelect(group.id)
        }
      }}
      aria-label={`Groupe ${group.name}`}
    >
      <div className="card-body character-card-body">
        <div className="character-card-header">
          <div>
            <h3 className="character-card-title">{group.name}</h3>
            <p className="stat-label">Membres: {memberCount}</p>
            <p className="stat-label">Places restantes: {remainingPlaces}</p>
            <p className="text-link" style={{ marginTop: '0.5rem' }}>
              Voir le detail
            </p>
          </div>

          <span className="character-hp-badge">Max {maxSize}</span>
        </div>
      </div>
    </article>
  )
}

export default GroupCard
