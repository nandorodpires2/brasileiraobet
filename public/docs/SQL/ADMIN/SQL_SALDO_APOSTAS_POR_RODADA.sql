select	p.partida_rodada,
			count(a.aposta_id) as apostas,
			sum(p.partida_valor) as montante,
			sum(a.aposta_vencedora_valor) as premio,
			sum(p.partida_valor) - sum(a.aposta_vencedora_valor) as saldo
from		aposta a 
			inner join partida p on a.partida_id = p.partida_id
where		p.partida_serie = 1
group by p.partida_rodada
order by p.partida_rodada desc