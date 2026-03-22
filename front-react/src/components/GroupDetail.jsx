import { useEffect, useState } from 'react'

function GroupDetail({ groupId, apiBaseUrl, onBack, onNavigateToCharacter }) {
  const [group, setGroup] = useState(null)
  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    const controller = new AbortController()

    async function loadGroupDetail() {
      setIsLoading(true)
      setError('')

      try {
        // Charge le detail complet du groupe avec sa liste de membres.
        const response = await fetch(`${apiBaseUrl}/api/v1/parties/${groupId}`, {
          signal: controller.signal,
        })

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`)
        }

        const detail = await response.json()
        setGroup(detail)
      } catch (err) {
        if (err.name !== 'AbortError') {
          setError('Impossible de charger le detail du groupe.')
        }
      } finally {
        setIsLoading(false)
      }
    }

    loadGroupDetail()

    return () => controller.abort()
  }, [groupId, apiBaseUrl])

  if (isLoading) {
    return <p className="section-text">Chargement du detail du groupe...</p>
  }

  if (error) {
    return <p className="is-invalid">{error}</p>
  }

  if (!group) {
    return <p className="section-text">Groupe introuvable.</p>
  }

  const maxSize = Number(group.maxSize ?? 0)
  const memberCount = Number(group.memberCount ?? 0)
  // Valeur affichee a l'ecran pour indiquer la capacite restante.
  const remainingPlaces = Math.max(0, maxSize - memberCount)
  const members = Array.isArray(group.members) ? group.members : []

  return (
    <section className="panel home-section" id="group-detail">
      <button type="button" className="button button-secondary" onClick={onBack}>
        Retour a la liste
      </button>

      <div className="detail-layout" style={{ marginTop: '1rem' }}>
        <article className="detail-card">
          <h2>{group.name}</h2>
          <p className="section-text">{group.description || 'Aucune description.'}</p>
          <div className="stats-grid" style={{ marginTop: '1rem' }}>
            <div className="stat-card">
              <div className="stat-value">{memberCount}</div>
              <p className="stat-label">Membres</p>
            </div>
            <div className="stat-card">
              <div className="stat-value">{remainingPlaces}</div>
              <p className="stat-label">Places restantes</p>
            </div>
          </div>
          <p className="section-text" style={{ marginTop: '1rem' }}>
            Places max: {maxSize}
          </p>
        </article>

        <article className="detail-card">
          <h3>Membres du groupe</h3>
          {members.length === 0 ? (
            <p className="section-text">Aucun membre dans ce groupe.</p>
          ) : (
            <ul className="simple-list">
              {members.map((member) => (
                <li key={member.id}>
                  <a
                    href="#characters"
                    className="text-link"
                    onClick={(event) => {
                      event.preventDefault()
                      onNavigateToCharacter(member.id)
                    }}
                  >
                    {member.name}
                  </a>
                  <span>
                    Niv. {member.level ?? '-'} - {member.class?.name || 'Classe inconnue'}
                  </span>
                </li>
              ))}
            </ul>
          )}
        </article>
      </div>
    </section>
  )
}

export default GroupDetail
