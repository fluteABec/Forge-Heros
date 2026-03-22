import { useState } from 'react'
import CharactersPage from './components/CharactersPage'
import GroupsPage from './components/GroupsPage'

function App() {
  const [currentPage, setCurrentPage] = useState('home')
  const [selectedCharacterId, setSelectedCharacterId] = useState(null)
  const [selectedGroupId, setSelectedGroupId] = useState(null)

  // Navigation interne simple sans react-router.
  const openPage = (page, event) => {
    event.preventDefault()
    setCurrentPage(page)
    if (page !== 'characters') {
      setSelectedCharacterId(null)
    }
    if (page !== 'groups') {
      setSelectedGroupId(null)
    }
  }

  const navigateToGroupDetail = (groupId = null) => {
    setCurrentPage('groups')
    setSelectedGroupId(groupId)
  }

  // Permet de sauter d'une vue a une autre avec un id cible preselectionne.
  const navigateToCharacterDetail = (characterId = null) => {
    setCurrentPage('characters')
    setSelectedCharacterId(characterId)
  }

  return (
    <div className="page-shell">
      <header className="topbar">
        <div>
          <a
            className="brand"
            href="#home"
            onClick={(event) => openPage('home', event)}
          >
            Forge de Heros
          </a>
          <nav className="nav-links" aria-label="Main navigation">
            <a
              href="#groups"
              className={currentPage === 'groups' ? 'active' : ''}
              onClick={(event) => openPage('groups', event)}
            >
              Groupes
            </a>
            <a
              href="#characters"
              className={currentPage === 'characters' ? 'active' : ''}
              onClick={(event) => openPage('characters', event)}
            >
              Personnages
            </a>
          </nav>
        </div>
      </header>

      <main className="main-content">
        {currentPage === 'home' && (
          <section className="hero hero-large">
            <div>
              <p className="eyebrow">Application React</p>
              <h1>Bienvenue dans la Forge</h1>
              <p className="hero-text">
                Cette application vous permet de consulter et gerer des
                personnages de jeu de role, leurs classes, leurs races et leurs
                groupes d&apos;aventure.
              </p>
              <div className="hero-actions">
                <a
                  className="button"
                  href="#groups"
                  onClick={(event) => openPage('groups', event)}
                >
                  Voir les groupes
                </a>
                <a
                  className="button button-secondary"
                  href="#characters"
                  onClick={(event) => openPage('characters', event)}
                >
                  Voir les personnages
                </a>
              </div>
            </div>
          </section>
        )}

        {currentPage === 'groups' && (
          <GroupsPage
            externalSelectedGroupId={selectedGroupId}
            onNavigateToCharacter={navigateToCharacterDetail}
          />
        )}

        {currentPage === 'characters' && (
          <CharactersPage
            onNavigateToGroups={navigateToGroupDetail}
            externalSelectedCharacterId={selectedCharacterId}
          />
        )}
      </main>
    </div>
  )
}

export default App
